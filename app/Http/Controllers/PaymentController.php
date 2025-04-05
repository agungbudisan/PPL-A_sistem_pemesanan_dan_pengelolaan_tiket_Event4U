<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
            'method' => 'required|string|max:255',
            'guest_email' => 'required|email',
        ]);

        $payment = Payment::create([
            'method' => $request->method,
            'status' => 'pending', // Default status
            'payment_date' => now(),
            'order_id' => $order->id,
            'guest_email' => $request->guest_email,
        ]);

        // Clear the session after successful payment
        session()->forget('guest_order_reference');

        return redirect()->route('guest.orders.confirmation', $reference)
            ->with('success', 'Payment processed successfully.');
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

        $payment->status = $request->status;
        $payment->save();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment status updated successfully.');
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
