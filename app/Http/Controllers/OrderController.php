<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('create', 'store');
    }

    public function create(Ticket $ticket)
    {
        return view('orders.create', compact('ticket'));
    }

    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'email' => 'required|email',
        ]);

        $totalPrice = $ticket->price * $request->quantity;
        
        Order::create([
            'total_price' => $totalPrice,
            'quantity' => $request->quantity,
            'email' => $request->email,
            'order_date' => now(),
            'ticket_id' => $ticket->id,
            'uid' => Auth::check() ? Auth::id() : null, // User ID or NULL for guest
        ]);

        return redirect()->route('orders.show', $ticket->id)->with('success', 'Order placed successfully.');
    }

    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }
}
