<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'index', 'show']);
        $this->middleware('admin')->only(['adminIndex', 'adminShow', 'updateStatus']);
    }

    /**
     * Display a listing of all payments for admin
     */
    public function adminIndex(Request $request)
    {
        $query = Payment::with(['order.ticket.event', 'order.user']);

        // Filter berdasarkan search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('order', function($q2) use ($search) {
                      $q2->where('reference', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order.user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order.ticket.event', function($q2) use ($search) {
                      $q2->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan metode pembayaran
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter berdasarkan event
        if ($request->filled('event_id')) {
            $eventId = $request->event_id;
            $query->whereHas('order.ticket', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Pengurutan
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_high':
                $query->orderByDesc(function($query) {
                    $query->select('total_price')
                        ->from('orders')
                        ->whereColumn('orders.id', 'payments.order_id');
                });
                break;
            case 'amount_low':
                $query->orderBy(function($query) {
                    $query->select('total_price')
                        ->from('orders')
                        ->whereColumn('orders.id', 'payments.order_id');
                });
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $payments = $query->paginate(15)->withQueryString();

        // Get data untuk dropdown filter
        $events = Event::orderBy('title')->get();

        // Statistik pembayaran
        $totalCompleted = Payment::where('status', 'completed')->count();
        $totalPending = Payment::where('status', 'pending')->count();
        $totalRevenue = Order::whereHas('payment', function($q) {
            $q->where('status', 'completed');
        })->sum('total_price');

        return view('admin.payments.index', compact('payments', 'events', 'totalCompleted', 'totalPending', 'totalRevenue'));
    }

    /**
     * Display a specified payment for admin
     */
    public function adminShow(Payment $payment)
    {
        $payment->load(['order.ticket.event', 'order.user']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled'
        ]);

        $payment->status = $request->status;
        $payment->updated_at = now();
        $payment->save();

        // Jika pembayaran disetujui (completed), update stok tiket
        if ($request->status === 'completed') {
            $order = $payment->order;
            $ticket = $order->ticket;

            // Kurangi stok tiket
            $ticket->quota_avail -= $order->quantity;
            $ticket->save();
        }

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Display the payment form for authenticated users
     */
    public function create(Order $order)
    {
        // Cek apakah order milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah order sudah memiliki payment yang completed
        if ($order->payment && $order->payment->status === 'completed') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah selesai.');
        }

        // Cek apakah order sudah melewati batas waktu pembayaran (1 jam)
        $orderTime = $order->order_date;
        $now = now();
        $diffInHours = $now->diffInHours($orderTime);

        if ($diffInHours >= 1) {
            return redirect()->route('orders.index')
                ->with('error', 'Batas waktu pembayaran telah berakhir.');
        }

        return view('payments.create', compact('order'));
    }

    /**
     * Store a payment for authenticated users
     */
    public function store(Request $request, Order $order)
    {
        // Cek apakah order milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validasi request
        $request->validate([
            'method' => 'required|in:transfer,credit_card,ewallet',
            'proof_file' => 'required|file|image|max:2048',
            'notes' => 'nullable|string|max:255',
        ]);

        // Upload file bukti pembayaran
        $path = $request->file('proof_file')->store('payments', 'public');

        // Buat atau update payment
        if ($order->payment) {
            $payment = $order->payment;
            $payment->method = $request->method;
            $payment->proof_file = $path;
            $payment->notes = $request->notes;
            $payment->status = 'pending';
            $payment->updated_at = now();
            $payment->save();
        } else {
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $request->method,
                'proof_file' => $path,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Bukti pembayaran berhasil dikirim. Admin akan memverifikasi pembayaran Anda.');
    }

    /**
     * Display the payment form for guest users
     */
    public function guestCreate($reference)
    {
        $order = Order::where('reference', $reference)
            ->with('ticket.event')
            ->firstOrFail();

        // Cek apakah order sudah memiliki payment yang completed
        if ($order->payment && $order->payment->status === 'completed') {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('info', 'Pembayaran untuk pesanan ini sudah selesai.');
        }

        // Cek apakah order sudah melewati batas waktu pembayaran (1 jam)
        $orderTime = $order->order_date;
        $now = now();
        $diffInHours = $now->diffInHours($orderTime);

        if ($diffInHours >= 1) {
            return redirect()->route('welcome')
                ->with('error', 'Batas waktu pembayaran telah berakhir.');
        }

        return view('guest.payments.create', compact('order'));
    }

    /**
     * Store a payment for guest users
     */
    public function guestStore(Request $request, $reference)
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        // Validasi request
        $request->validate([
            'method' => 'required|in:transfer,credit_card,ewallet',
            'proof_file' => 'required|file|image|max:2048',
            'notes' => 'nullable|string|max:255',
        ]);

        // Upload file bukti pembayaran
        $path = $request->file('proof_file')->store('payments', 'public');

        // Buat atau update payment
        if ($order->payment) {
            $payment = $order->payment;
            $payment->method = $request->method;
            $payment->proof_file = $path;
            $payment->notes = $request->notes;
            $payment->status = 'pending';
            $payment->updated_at = now();
            $payment->save();
        } else {
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $request->method,
                'proof_file' => $path,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('guest.orders.confirmation', $reference)
            ->with('success', 'Bukti pembayaran berhasil dikirim. Admin akan memverifikasi pembayaran Anda.');
    }

    /**
     * Display user's payments
     */
    public function index()
    {
        $payments = Payment::whereHas('order', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->with(['order.ticket.event'])
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    /**
     * Display a specified payment for authenticated user
     */
    public function show(Payment $payment)
    {
        // Cek apakah payment terkait dengan order milik user yang login
        if ($payment->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $payment->load(['order.ticket.event']);

        return view('payments.show', compact('payment'));
    }
}
