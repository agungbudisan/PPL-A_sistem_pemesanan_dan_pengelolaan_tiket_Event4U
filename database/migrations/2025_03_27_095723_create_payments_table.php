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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('method');
            $table->string('payment_method_detail')->nullable();
            $table->string('status');
            $table->string('guest_email')->nullable();
            $table->dateTime('payment_date');
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('order_id')->constrained('orders');
            $table->text('payment_instruction')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
