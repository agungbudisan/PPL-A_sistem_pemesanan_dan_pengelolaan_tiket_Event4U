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

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'show']);
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

        return view('payments.create', compact('order'));
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

        $request->validate([
            'method' => 'required|string|in:transfer,ewallet,credit_card',
        ]);

        try {
            // For demonstration purposes, immediately set payment status to pending
            // In production, you would determine this based on your payment gateway
            $paymentStatus = 'pending'; // Options: pending, completed, failed, cancelled

            // Create payment record
            $payment = Payment::create([
                'method' => $request->method,
                'status' => $paymentStatus,
                'payment_date' => now(),
                'order_id' => $order->id,
            ]);

            // If using a real payment gateway, you would redirect to the payment gateway here
            // and handle the callback in a separate method.

            // Jika pembayaran berhasil (status completed), proses email dan kurangi kuota
            if ($paymentStatus === 'completed') {
                // Kurangi kuota tiket
                $order->ticket->decrement('quota_avail', $order->quantity);

                try {
                    // Kirim e-ticket
                    $order = $order->fresh(['ticket.event', 'user']);
                    Mail::to($order->email)->send(new SendETicket($order, false));
                    Log::info('E-ticket sent to registered user: ' . $order->email);
                } catch (\Exception $e) {
                    Log::error('Failed to send e-ticket: ' . $e->getMessage());
                }
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pembayaran berhasil diproses. Menunggu konfirmasi pembayaran.');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Payment processing error: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
        }
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
            'method' => 'required|string|in:transfer,ewallet,credit_card',
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

        try {
            // For demonstration purposes, set payment status to completed
            // In production, you would determine this based on your payment gateway
            $paymentStatus = 'completed'; // Simulasi status pembayaran berhasil

            // Create payment record
            $payment = Payment::create([
                'method' => $request->method,
                'status' => $paymentStatus,
                'payment_date' => now(),
                'order_id' => $order->id,
                'guest_email' => $request->guest_email,
            ]);

            // Jika pembayaran berhasil (status completed), proses email dan kurangi kuota
            if ($paymentStatus === 'completed') {
                // Kurangi kuota tiket
                $order->ticket->decrement('quota_avail', $order->quantity);
                Log::info('Ticket quota decreased by ' . $order->quantity . ' for ticket ID: ' . $order->ticket->id);

                try {
                    // Kirim e-ticket dengan eager loading untuk memastikan semua relasi dimuat
                    $order = $order->fresh(['ticket.event']);
                    Mail::to($order->email)->send(new SendETicket($order, true));
                    Log::info('Guest e-ticket sent to: ' . $order->email);
                } catch (\Exception $e) {
                    Log::error('Failed to send guest e-ticket: ' . $e->getMessage());
                }
            }

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

    /**
     * Method untuk menguji pengiriman email
     */
    // public function testSendEmail(Request $request)
    // {
    //     // Hanya bisa diakses di environment local
    //     if (app()->environment() !== 'local') {
    //         abort(403, 'Hanya tersedia di development environment.');
    //     }

    //     $orderId = $request->input('order_id');
    //     if (!$orderId) {
    //         return response()->json(['error' => 'Order ID diperlukan'], 400);
    //     }

    //     // Eager load semua relasi yang dibutuhkan
    //     $order = Order::with(['ticket.event', 'user'])->findOrFail($orderId);

    //     // Log semua info order untuk debugging
    //     Log::info('Order details for testing:');
    //     Log::info('Order ID: ' . $order->id);
    //     Log::info('Reference: ' . $order->reference);
    //     Log::info('Email: ' . $order->email);

    //     $isGuest = $order->user_id === null;
    //     Log::info('Is guest order: ' . ($isGuest ? 'Yes' : 'No'));

    //     try {
    //         // Kirim email langsung tanpa queue untuk testing
    //         Mail::to($order->email)->send(new SendETicket($order, $isGuest));

    //         Log::info('Email berhasil dikirim ke ' . $order->email);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Email berhasil dikirim ke ' . $order->email
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error saat mengirim email: ' . $e->getMessage());
    //         Log::error($e->getTraceAsString());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error saat mengirim email: ' . $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ], 500);
    //     }
    // }
}
