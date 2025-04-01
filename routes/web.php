<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public Routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

// Order Routes (protected with auth)
Route::middleware('auth')->group(function () {
    Route::get('/tickets/{ticket}/order', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/tickets/{ticket}/order', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Payment Routes
    Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
});

// Route Admin
Route::middleware('admin')->prefix('admin')->group(function () {
    // Dashboard & Analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/analytics/{eventId?}', [DashboardController::class, 'analytics'])->name('admin.analytics');

    // Event Management
    Route::resource('events', EventController::class)->except(['show']);
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

    // Ticket Management untuk Event tertentu
    Route::get('/events/{event}/tickets', [TicketController::class, 'index'])->name('events.tickets.index');
    Route::get('/events/{event}/tickets/create', [TicketController::class, 'create'])->name('events.tickets.create');
    Route::post('/events/{event}/tickets', [TicketController::class, 'store'])->name('events.tickets.store');

    // Ticket Resource untuk operasi edit, update, destroy
    Route::resource('tickets', TicketController::class)->only(['edit', 'update', 'destroy']);

    // Category Management
    Route::resource('categories', CategoryController::class);
});

require __DIR__.'/auth.php';
