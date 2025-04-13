<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'show']);
        $this->middleware('admin')->only(['adminIndex']);
    }

    /**
     * Display a listing of payments for admin
     */
    public function adminIndex(Request $request)
    {
        $query = Payment::with(['order.ticket.event', 'order.user'])
            ->orderBy('payment_date', 'desc');

        // Apply filters if needed
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('method', 'like', "%{$search}%")
                ->orWhereHas('order', function($subq) use ($search) {
                    $subq->where('email', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('event_id') && !empty($request->event_id)) {
            $query->whereHas('order.ticket', function($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        // Paginate the results (15 per page, adjust as needed)
        $payments = $query->paginate(15);

        // Get all events for filter dropdown
        $events = \App\Models\Event::orderBy('title')->get();

        return view('admin.payments.index', compact('payments', 'events'));
    }

    /**
     * Show the form for creating a new payment for authenticated user
     */
    public function create(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->uid !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('payments.create', compact('order'));
    }

    /**
     * Store a newly created payment for authenticated user
     */
    public function store(Request $request, Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->uid !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'method' => 'required|string|max:255',
        ]);

        $payment = Payment::create([
            'method' => $request->method,
            'status' => 'pending', // Default status
            'payment_date' => now(),
            'order_id' => $order->id,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Payment processed successfully.');
    }

    /**
     * Show the form for creating a new payment for guest user
     */
    public function guestCreate($reference)
    {
        $order = Order::where('reference', $reference)
            ->with('ticket.event')
            ->firstOrFail();

        // Check if the reference matches the one in session
        if (!session('guest_order_reference') || session('guest_order_reference') !== $reference) {
            abort(403, 'Unauthorized action.');
        }

        return view('guest.payments.create', compact('order'));
    }

    /**
     * Store a newly created payment for guest user
     */
    public function guestStore(Request $request, $reference)
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        // Check if the reference matches the one in session
        if (!session('guest_order_reference') || session('guest_order_reference') !== $reference) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'method' => 'required|string|in:transfer,ewallet',
            'guest_email' => 'required|email',
        ]);

        // Pastikan email yang digunakan sesuai dengan email pada order
        if ($order->email !== $request->guest_email) {
            return back()->withErrors([
                'guest_email' => 'Email yang dimasukkan tidak sesuai dengan email pada pesanan.'
            ])->withInput();
        }

        // Check if payment already exists
        if ($order->payment) {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('info', 'Pembayaran untuk pesanan ini sudah dilakukan sebelumnya.');
        }

        // For demonstration purposes, immediately set payment status to completed
        // In production, you would integrate with actual payment gateways
        $paymentStatus = 'pending'; // Options: pending, completed, failed, cancelled

        try {
            // Create payment record
            $payment = Payment::create([
                'method' => $request->method,
                'status' => $paymentStatus,
                'payment_date' => now(),
                'order_id' => $order->id,
                'guest_email' => $request->guest_email,
            ]);

            // Update ticket quota
            // $ticket = $order->ticket;
            // $ticket->decrement('quota_avail', $order->quantity);

            // If payment completed successfully, send e-ticket email
            // if ($paymentStatus === 'completed') {
            //     try {
            //         Mail::to($order->email)->send(new ETicketMail($order));
            //     } catch (\Exception $e) {
            //         // Log the error but continue with the process
            //         \Log::error('Failed to send e-ticket email: ' . $e->getMessage());
            //     }
            // }

            // Clear the session after successful payment
            session()->forget('guest_order_reference');

            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('success', 'Pembayaran berhasil diproses. E-ticket telah dikirim ke email Anda.');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Payment processing error: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        // Check if payment belongs to authenticated user
        if ($payment->order->uid !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('payments.show', compact('payment'));
    }

    /**
     * Update payment status (for admin)
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|string|in:pending,completed,cancelled,failed'
        ]);

        $oldStatus = $payment->status;
        $payment->status = $request->status;
        $payment->save();

        // Jika status diubah dari selain completed menjadi completed
        if ($oldStatus !== 'completed' && $request->status === 'completed') {
            // Kurangi kuota tiket
            $payment->order->ticket->decrement('quota_avail', $payment->order->quantity);

            // Kirim e-ticket
            // Mail::to($payment->order->email)->send(new ETicketMail($payment->order));
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Display order confirmation for guest user
     */
    public function guestConfirmation($reference)
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        // Check if the reference matches the one in session
        if (!session('guest_order_reference') || session('guest_order_reference') !== $reference) {
            abort(403, 'Unauthorized action.');
        }

        return view('guest.orders.confirmation', compact('order'));
    }
}
