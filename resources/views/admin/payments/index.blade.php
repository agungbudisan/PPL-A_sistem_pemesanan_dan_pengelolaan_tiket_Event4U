@extends('admin.layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Pembayaran</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Daftar semua pembayaran dari pengguna.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Total Payments -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-full p-3">
                <i class="fas fa-money-check-alt text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pembayaran</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $payments->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Completed Payments -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pembayaran Selesai</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalCompleted }}</p>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-500 rounded-full p-3">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu Verifikasi</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalPending }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filter & Pencarian</h3>
    </div>
    <div class="p-6">
        <form action="{{ route('admin.payments.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Search Field -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Cari berdasarkan ID, nama pembeli, email..."
                    >
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
                    <select
                        name="status"
                        id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>

                <!-- Payment Method Filter -->
                <div>
                    <label for="method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode Pembayaran</label>
                    <select
                        name="method"
                        id="method"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Semua Metode</option>
                        <option value="transfer" {{ request('method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                        <option value="ewallet" {{ request('method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </div>

                <!-- Event Filter -->
                <div>
                    <label for="event" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acara</label>
                    <select
                        name="event_id"
                        id="event"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Semua Acara</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                    <input
                        type="date"
                        name="date_from"
                        id="date_from"
                        value="{{ request('date_from') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                    <input
                        type="date"
                        name="date_to"
                        id="date_to"
                        value="{{ request('date_to') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                <!-- Sort By -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urutkan Berdasarkan</label>
                    <select
                        name="sort"
                        id="sort"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Nominal Tertinggi</option>
                        <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Nominal Terendah</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-search mr-2"></i>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Pembayaran</h3>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Total: {{ $payments->total() }} pembayaran
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acara</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pembeli</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Metode</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($payments as $payment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        #{{ $payment->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $payment->order->ticket->event->title ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $payment->order->user->name ?? $payment->order->guest_name ?? 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $payment->order->email }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @if($payment->method === 'transfer')
                            <span class="inline-flex items-center">
                                <i class="fas fa-university mr-2"></i> Transfer Bank
                            </span>
                        @elseif($payment->method === 'credit_card')
                            <span class="inline-flex items-center">
                                <i class="fas fa-credit-card mr-2"></i> Kartu Kredit
                            </span>
                        @elseif($payment->method === 'ewallet')
                            <span class="inline-flex items-center">
                                <i class="fas fa-wallet mr-2"></i> E-Wallet
                            </span>
                        @else
                            {{ ucfirst($payment->method) }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        Rp {{ number_format($payment->order->total_price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $payment->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            $statusIcon = 'fa-question-circle';

                            if($payment->status === 'pending') {
                                $statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300';
                                $statusIcon = 'fa-clock';
                            } elseif($payment->status === 'completed') {
                                $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
                                $statusIcon = 'fa-check-circle';
                            } elseif($payment->status === 'cancelled') {
                                $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
                                $statusIcon = 'fa-times-circle';
                            } elseif($payment->status === 'failed') {
                                $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
                                $statusIcon = 'fa-exclamation-circle';
                            }
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }} mr-1"></i>
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.payments.show', $payment) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="py-8">
                            <i class="fas fa-money-check text-4xl mb-3 text-gray-400 dark:text-gray-600"></i>
                            <p class="text-lg font-medium">Tidak ada pembayaran yang ditemukan</p>
                            <p class="text-sm mt-1">Coba ubah filter pencarian</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $payments->withQueryString()->links() }}
</div>
@endsection
