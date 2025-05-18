<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanExpiredOrders extends Command
{
    protected $signature = 'orders:clean-expired';
    protected $description = 'Clean up expired orders that have not been paid';

    public function handle()
    {
        DB::beginTransaction();
        try {
            $expiredOrders = Order::where('expires_at', '<', now())
                ->whereDoesntHave('payment', function ($query) {
                    $query->where('status', 'completed');
                })
                ->orWhereHas('payment', function ($query) {
                    $query->whereIn('status', ['pending', null])
                        ->where('expires_at', '<', now());
                })
                ->get();

            $count = $expiredOrders->count();
            $this->info("Found {$count} expired orders to clean up.");

            foreach ($expiredOrders as $order) {
                // If there's a pending payment, mark it as expired
                if ($order->payment && $order->payment->status === 'pending') {
                    $order->payment->status = 'expired';
                    $order->payment->save();
                    $this->info("Marked payment {$order->payment->id} as expired for order {$order->id}");
                }

                // Log the expired order
                Log::info("Order {$order->id} expired and has been cleaned up");
            }

            DB::commit();
            $this->info("Expired orders cleanup completed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error cleaning up expired orders: {$e->getMessage()}");
            Log::error("Error cleaning up expired orders: {$e->getMessage()}");
        }
    }
}
