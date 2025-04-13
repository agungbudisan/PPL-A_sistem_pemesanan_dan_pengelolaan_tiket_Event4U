<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'show', 'index', 'downloadETicketPdf']);
        $this->middleware('admin')->only(['adminIndex', 'adminShow']);
    }

    /**
     * Display a listing of orders for authenticated user
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with(['ticket.event', 'payment']);

        // Search by event title
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('ticket.event', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Filter by payment status
        if ($request->has('status') && !empty($request->status)) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('total_price', 'desc');
                    break;
                case 'price_low':
                    $query->orderBy('total_price', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orders = $query->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display a listing of all orders for admin
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['ticket.event', 'payment', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters if needed
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('ticket.event', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order for authenticated user
     */
    public function create(Ticket $ticket, Request $request)
    {
        // Gunakan single query parameter 'qty' untuk jumlah tiket
        $quantity = $request->query('qty', 1);

        // Konversi ke integer
        $quantity = (int) $quantity;

        // Validasi quantity
        if ($quantity < 1 || $quantity > 5) {
            $quantity = 1; // Default jika invalid
        }

        // Pastikan tidak melebihi quota_avail
        $quantity = min($quantity, $ticket->quota_avail);

        return view('orders.create', compact('ticket', 'quantity'));
    }

    /**
     * Store a newly created order for authenticated user
     */
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'email' => 'required|email',
        ]);

        $totalPrice = $ticket->price * $request->quantity;

        $order = Order::create([
            'total_price' => $totalPrice,
            'quantity' => $request->quantity,
            'email' => $request->email,
            'order_date' => now(),
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('payments.create', $order)->with('success', 'Order placed successfully. Proceed to payment.');
    }

    /**
     * Show the form for creating a new order for guest user
     */
    public function guestCreate(Request $request, Ticket $ticket)
    {
        // Gunakan single query parameter 'qty' untuk jumlah tiket
        $quantity = $request->query('qty', 1);

        // Konversi ke integer
        $quantity = (int) $quantity;

        // Validasi quantity
        if ($quantity < 1 || $quantity > 5) {
            $quantity = 1; // Default jika invalid
        }

        // Pastikan tidak melebihi quota_avail
        $quantity = min($quantity, $ticket->quota_avail);

        return view('guest.orders.create', compact('ticket', 'quantity'));
    }

    /**
     * Store a newly created order for guest user
     */
    public function guestStore(Request $request, Ticket $ticket)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $totalPrice = $ticket->price * $request->quantity;
        $reference = 'ORD-' . Str::upper(Str::random(8));

        $order = Order::create([
            'total_price' => $totalPrice,
            'quantity' => $request->quantity,
            'email' => $request->email,
            'order_date' => now(),
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'guest_name' => $request->name,
            'guest_phone' => $request->phone,
            'reference' => $reference,
        ]);

        // Store order reference in session for guest users
        session(['guest_order_reference' => $reference]);

        return redirect()->route('guest.payments.create', $reference)
            ->with('success', 'Order placed successfully. Proceed to payment.');
    }

    /**
     * Display the specified order for authenticated user
     */
    public function show(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Display the specified order for admin
     */
    public function adminShow(Order $order)
    {
        $order->load(['ticket.event', 'payment', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Display order confirmation for guest user
     */
    public function guestConfirmation($reference)
    {
        $order = Order::where('reference', $reference)
            ->with(['ticket.event', 'payment'])
            ->firstOrFail();

        return view('guest.orders.confirmation', compact('order'));
    }

    /**
     * Download e-ticket PDF for authenticated user
     */
    public function downloadETicketPdf(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Pastikan order punya payment dengan status completed
        if (!$order->payment || $order->payment->status !== 'completed') {
            return back()->with('error', 'E-ticket hanya tersedia setelah pembayaran dikonfirmasi oleh admin.');
        }

        $pdf = Pdf::loadView('pdfs.eticket', compact('order'));

        return $pdf->download('e-ticket-' . $order->id . '.pdf');
    }

    /**
     * Download e-ticket PDF for guest user
     */
    public function downloadETicketPdfGuest($reference)
    {
        $order = Order::where('reference', $reference)
            ->with(['ticket.event', 'payment'])
            ->firstOrFail();

        // Pastikan order punya payment dengan status completed
        if (!$order->payment || $order->payment->status !== 'completed') {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('error', 'E-ticket hanya tersedia setelah pembayaran dikonfirmasi oleh admin.');
        }

        $pdf = Pdf::loadView('pdfs.guest-eticket', compact('order'));

        return $pdf->download('e-ticket-' . $reference . '.pdf');
    }
}
