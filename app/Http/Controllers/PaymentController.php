<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendETicket;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'show', 'processMidtransPayment', 'finishMidtransPayment']);
        $this->middleware('admin')->only(['adminIndex', 'updateStatus']);
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
                ->orWhere('transaction_id', 'like', "%{$search}%")
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
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order was created within the last hour
        $orderCreatedAt = $order->order_date;
        $now = now();
        $diffInHours = $now->diffInHours($orderCreatedAt);

        if ($diffInHours >= 1) {
            return redirect()->route('orders.index')
                ->with('error', 'Batas waktu pembayaran telah habis. Silakan buat pesanan baru.');
        }

        // Check if payment already exists
        if ($order->payment) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah dilakukan sebelumnya.');
        }

        // Langsung arahkan ke pembayaran Midtrans
        return redirect()->route('payments.midtrans', $order);
    }

    /**
     * Store a newly created payment for authenticated user
     */
    public function store(Request $request, Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order was created within the last hour
        $orderCreatedAt = $order->order_date;
        $now = now();
        $diffInHours = $now->diffInHours($orderCreatedAt);

        if ($diffInHours >= 1) {
            return redirect()->route('orders.index')
                ->with('error', 'Batas waktu pembayaran telah habis. Silakan buat pesanan baru.');
        }

        // Check if payment already exists
        if ($order->payment) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah dilakukan sebelumnya.');
        }

        // Langsung arahkan ke pembayaran Midtrans
        return redirect()->route('payments.midtrans', $order);
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

        // Langsung arahkan ke pembayaran Midtrans
        return redirect()->route('guest.payments.midtrans', $reference);
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

        // Check if payment already exists
        if ($order->payment) {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('info', 'Pembayaran untuk pesanan ini sudah dilakukan sebelumnya.');
        }

        // Langsung arahkan ke pembayaran Midtrans
        return redirect()->route('guest.payments.midtrans', $reference);
    }

    /**
     * Memproses pembayaran dengan Midtrans untuk user terotentikasi
     */
    public function processMidtransPayment(Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the order was created within the last hour
        $orderCreatedAt = $order->order_date;
        $now = now();
        $diffInHours = $now->diffInHours($orderCreatedAt);

        if ($diffInHours >= 1) {
            return redirect()->route('orders.index')
                ->with('error', 'Batas waktu pembayaran telah habis. Silakan buat pesanan baru.');
        }

        // Check if payment already exists
        if ($order->payment) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah dilakukan sebelumnya.');
        }

        try {
            // Setting Midtrans configuration
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            // Buat ID transaksi unik
            $transactionId = 'ORDER-' . $order->id . '-' . Str::random(8);

            // Set parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $transactionId,
                    'gross_amount' => (int) $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email' => $order->email,
                ],
                'item_details' => [
                    [
                        'id' => $order->ticket->id,
                        'price' => (int) $order->ticket->price,
                        'quantity' => $order->quantity,
                        'name' => $order->ticket->event->title . ' - ' . $order->ticket->ticket_class,
                    ],
                ],
                'callbacks' => [
                    'finish' => route('payments.midtrans.finish', $order->id),
                ],
            ];

            // Dapatkan Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Buat payment record
            $payment = Payment::create([
                'method' => 'midtrans',
                'status' => 'pending',
                'transaction_id' => $transactionId,
                'snap_token' => $snapToken,
                'payment_date' => now(),
                'order_id' => $order->id,
            ]);

            // Return view dengan snap token
            return view('payments.midtrans', [
                'order' => $order,
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memproses pembayaran dengan Midtrans untuk guest user
     */
    public function processMidtransPaymentGuest($reference)
    {
        $order = Order::where('reference', $reference)
            ->with('ticket.event')
            ->firstOrFail();

        // Verifikasi referensi di session
        if (!session('guest_order_reference') || session('guest_order_reference') !== $reference) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah sudah ada pembayaran
        if ($order->payment) {
            return redirect()->route('guest.orders.confirmation', $reference)
                ->with('info', 'Pembayaran untuk order ini sudah dibuat.');
        }

        try {
            // Setting Midtrans configuration
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            // Buat ID transaksi unik
            $transactionId = 'GUEST-' . $reference . '-' . Str::random(8);

            // Set parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $transactionId,
                    'gross_amount' => (int) $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $order->guest_name,
                    'email' => $order->email,
                    'phone' => $order->guest_phone,
                ],
                'item_details' => [
                    [
                        'id' => $order->ticket->id,
                        'price' => (int) $order->ticket->price,
                        'quantity' => $order->quantity,
                        'name' => $order->ticket->event->title . ' - ' . $order->ticket->ticket_class,
                    ],
                ],
                'callbacks' => [
                    'finish' => route('guest.payments.midtrans.finish', $reference),
                ],
            ];

            // Dapatkan Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Buat payment record
            $payment = Payment::create([
                'method' => 'midtrans',
                'status' => 'pending',
                'transaction_id' => $transactionId,
                'snap_token' => $snapToken,
                'payment_date' => now(),
                'order_id' => $order->id,
                'guest_email' => $order->email,
            ]);

            // Return view dengan snap token
            return view('guest.payments.midtrans', [
                'order' => $order,
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Handle callback setelah pembayaran selesai (untuk User)
     */
    public function finishMidtransPayment(Order $order, Request $request)
    {
        // Verifikasi hak akses
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah ada data transaksi dari Midtrans
        if ($request->has('transaction_status') && $request->has('order_id')) {
            // Cari payment berdasarkan transaction_id
            $payment = Payment::where('transaction_id', $request->order_id)
                ->where('order_id', $order->id)
                ->first();

            if ($payment) {
                // Update status payment berdasarkan status transaksi
                $transactionStatus = $request->transaction_status;

                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $payment->status = 'completed';
                    $payment->save();

                    // Proses e-ticket
                    $order->ticket->decrement('quota_avail', $order->quantity);

                    try {
                        $order = $order->fresh(['ticket.event', 'user']);
                        Mail::to($order->email)->send(new SendETicket($order, false));
                        Log::info('E-ticket sent to registered user: ' . $order->email);
                    } catch (\Exception $e) {
                        Log::error('Failed to send e-ticket: ' . $e->getMessage());
                    }

                    return redirect()->route('orders.show', $order)
                        ->with('success', 'Pembayaran berhasil! E-ticket telah dikirim ke email Anda.');
                } else if ($transactionStatus == 'pending') {
                    return redirect()->route('orders.show', $order)
                        ->with('info', 'Pembayaran sedang dalam proses. Kami akan mengirim e-ticket setelah pembayaran dikonfirmasi.');
                } else {
                    return redirect()->route('orders.show', $order)
                        ->with('error', 'Pembayaran gagal atau dibatalkan.');
                }
            }
        }

        return redirect()->route('orders.show', $order)
            ->with('info', 'Status pembayaran akan diperbarui segera.');
    }

    /**
     * Handle callback setelah pembayaran selesai (untuk Guest)
     */
    public function finishMidtransPaymentGuest($reference, Request $request)
    {
        $order = Order::where('reference', $reference)->firstOrFail();

        // Cek apakah ada data transaksi dari Midtrans
        if ($request->has('transaction_status') && $request->has('order_id')) {
            // Cari payment berdasarkan transaction_id
            $payment = Payment::where('transaction_id', $request->order_id)
                ->where('order_id', $order->id)
                ->first();

            if ($payment) {
                // Update status payment berdasarkan status transaksi
                $transactionStatus = $request->transaction_status;

                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $payment->status = 'completed';
                    $payment->save();

                    // Proses e-ticket
                    $order->ticket->decrement('quota_avail', $order->quantity);

                    try {
                        $order = $order->fresh(['ticket.event']);
                        Mail::to($order->email)->send(new SendETicket($order, true));
                        Log::info('E-ticket sent to guest: ' . $order->email);
                    } catch (\Exception $e) {
                        Log::error('Failed to send e-ticket: ' . $e->getMessage());
                    }

                    // Hapus referensi dari session
                    session()->forget('guest_order_reference');

                    return redirect()->route('guest.orders.confirmation', $reference)
                        ->with('success', 'Pembayaran berhasil! E-ticket telah dikirim ke email Anda.');
                } else if ($transactionStatus == 'pending') {
                    return redirect()->route('guest.orders.confirmation', $reference)
                        ->with('info', 'Pembayaran sedang dalam proses. Kami akan mengirim e-ticket setelah pembayaran dikonfirmasi.');
                } else {
                    return redirect()->route('guest.orders.confirmation', $reference)
                        ->with('error', 'Pembayaran gagal atau dibatalkan.');
                }
            }
        }

        return redirect()->route('guest.orders.confirmation', $reference)
            ->with('info', 'Status pembayaran akan diperbarui segera.');
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        // Check if payment belongs to authenticated user
        if ($payment->order->user_id !== Auth::id()) {
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
        $newStatus = $request->status;

        // Hanya update jika status berubah
        if ($oldStatus !== $newStatus) {
            $payment->status = $newStatus;
            $payment->save();

            // Jika status diubah dari selain completed menjadi completed
            if ($oldStatus !== 'completed' && $newStatus === 'completed') {
                // Load order dengan relasi yang diperlukan
                $order = $payment->order->load(['ticket.event', 'user']);

                // Kurangi kuota tiket
                $order->ticket->decrement('quota_avail', $order->quantity);
                Log::info('Ticket quota decreased by ' . $order->quantity . ' for ticket ID: ' . $order->ticket->id);

                // Kirim e-ticket
                try {
                    $isGuest = $order->user_id === null; // Tentukan apakah user adalah guest
                    Mail::to($order->email)->send(new SendETicket($order, $isGuest));
                    Log::info('E-ticket sent to ' . ($isGuest ? 'guest' : 'user') . ': ' . $order->email);
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim e-ticket: ' . $e->getMessage());
                }
            }
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
