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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('location');
            $table->dateTime('start_event');
            $table->dateTime('end_event');
            $table->dateTime('start_sale');
            $table->dateTime('end_sale');
            $table->string('thumbnail');
            $table->string('stage_layout')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('uid_admin')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
