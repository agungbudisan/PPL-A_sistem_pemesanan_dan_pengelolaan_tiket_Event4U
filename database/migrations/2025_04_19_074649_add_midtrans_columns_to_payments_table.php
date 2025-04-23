<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('status');
            $table->string('snap_token')->nullable()->after('transaction_id');
            $table->string('payment_type')->nullable()->after('snap_token');
            $table->json('payment_data')->nullable()->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
                'snap_token',
                'payment_type',
                'payment_data'
            ]);
        });
    }
};
