<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Order $order)
    {
        return view('payments.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $request->validate([
            'method' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        Payment::create([
            'method' => $request->method,
            'status' => $request->status,
            'payment_date' => now(),
            'order_id' => $order->id,
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Payment processed successfully.');
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }
}
