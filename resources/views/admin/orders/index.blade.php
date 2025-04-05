@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Pesanan</h1>
</div>

<!-- Filter and Search -->
<form action="{{ route('admin.orders.index') }}" method="GET" class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Pesanan</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 dark:text-white sm:text-sm"
                    placeholder="Cari berdasarkan email, referensi..."
                >
            </div>
        </div>
        <div>
            <label for="event_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acara</label>
            <select
                name="event_id"
                id="event_id"
                class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm rounded-md bg-white dark:bg-gray-700 dark:text-white"
            >
                <option value="">Semua Acara</option>
                @foreach($events ?? [] as $event)
                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
            <select
                name="status"
                id="status"
                class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm rounded-md bg-white dark:bg-gray-700 dark:text-white"
            >
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                <option value="none" {{ request('status') == 'none' ? 'selected' : '' }}>Belum Bayar</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            @if(request('search') || request('event_id') || request('status'))
                <a href="{{ route('admin.orders.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-times mr-2"></i> Reset
                </a>
            @endif
        </div>
    </div>

    <!-- Results summary -->
    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
        <p>Menampilkan {{ $orders->count() }} pesanan</p>
    </div>
</form>

<!-- Orders Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID/Referensi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acara</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tiket</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pembeli</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($orders as $index => $order)
                <tr class="{{ $index % 2 == 0 ? '' : 'bg-gray-50 dark:bg-gray-700' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $order->reference ?? '#'.str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $order->ticket->event->title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->ticket->name ?? $order->ticket->ticket_class }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            @if($order->uid)
                                {{ $order->user->name ?? 'User #'.$order->uid }}
                                <span class="text-xs text-indigo-600 dark:text-indigo-400">(Terdaftar)</span>
                            @else
                                {{ $order->guest_name ?? $order->email }}
                                <span class="text-xs text-gray-500 dark:text-gray-400">(Tamu)</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @if(is_string($order->order_date))
                                {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y, H:i') }}
                            @else
                                {{ $order->order_date->format('d M Y, H:i') }}
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->quantity }} tiket</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @php
                            $statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            $statusText = 'Belum Bayar';

                            if(isset($order->payment)) {
                                if($order->payment->status == 'pending') {
                                    $statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300';
                                    $statusText = 'Menunggu';
                                } elseif($order->payment->status == 'completed') {
                                    $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
                                    $statusText = 'Selesai';
                                } elseif($order->payment->status == 'cancelled') {
                                    $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
                                    $statusText = 'Dibatalkan';
                                } elseif($order->payment->status == 'failed') {
                                    $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
                                    $statusText = 'Gagal';
                                }
                            }
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="py-8">
                            <i class="fas fa-search text-4xl mb-3 text-gray-400 dark:text-gray-600"></i>
                            <p class="text-lg font-medium">Tidak ada pesanan yang ditemukan</p>
                            <p class="text-sm mt-1">Coba ubah filter pencarian</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Order Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <!-- Total Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-full p-3">
                <i class="fas fa-shopping-cart text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pesanan</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $orders->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Rp {{ number_format($orders->sum('total_price'), 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Completed Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-full p-3">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pesanan Selesai</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $orders->filter(function($order) {
                        return isset($order->payment) && $order->payment->status == 'completed';
                    })->count() }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
