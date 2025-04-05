<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'show', 'index']);
        $this->middleware('admin')->only(['adminIndex', 'adminShow']);
    }

    /**
     * Display a listing of orders for authenticated user
     */
    public function index()
    {
        $orders = Order::where('uid', Auth::id())
            ->with(['ticket.event', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Display a listing of all orders for admin
     */
    public function adminIndex()
    {
        $orders = Order::with(['ticket.event', 'payment', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order for authenticated user
     */
    public function create(Ticket $ticket)
    {
        return view('orders.create', compact('ticket'));
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
            'uid' => Auth::id(),
        ]);

        return redirect()->route('payments.create', $order)->with('success', 'Order placed successfully. Proceed to payment.');
    }

    /**
     * Show the form for creating a new order for guest user
     */
    public function guestCreate(Ticket $ticket)
    {
        return view('guest.orders.create', compact('ticket'));
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
            'uid' => null,
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
        if ($order->uid !== Auth::id()) {
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
}
