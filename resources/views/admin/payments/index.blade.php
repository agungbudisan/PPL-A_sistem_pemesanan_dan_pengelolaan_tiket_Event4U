@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Pembayaran</h1>
</div>

<!-- Filter and Search -->
<form action="{{ route('admin.payments.index') }}" method="GET" class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Pembayaran</label>
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
                    placeholder="Cari berdasarkan metode, email..."
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
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
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
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            @if(request('search') || request('event_id') || request('status'))
                <a href="{{ route('admin.payments.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-times mr-2"></i> Reset
                </a>
            @endif
        </div>
    </div>

    <!-- Results summary -->
    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
        <p>Menampilkan {{ $payments->count() }} pembayaran</p>
    </div>
</form>

<!-- Payments Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acara</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pembeli</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($payments as $index => $payment)
                <tr class="{{ $index % 2 == 0 ? '' : 'bg-gray-50 dark:bg-gray-700' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            #{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $payment->order->ticket->event->title }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->order->ticket->name ?? $payment->order->ticket->ticket_class }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            @if($payment->order->uid)
                                {{ $payment->order->user->name ?? 'User #'.$payment->order->uid }}
                                <span class="text-xs text-indigo-600 dark:text-indigo-400">(Terdaftar)</span>
                            @else
                                {{ $payment->order->guest_name ?? ($payment->guest_email ?? $payment->order->email) }}
                                <span class="text-xs text-gray-500 dark:text-gray-400">(Tamu)</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->guest_email ?? $payment->order->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $payment->method }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @if(is_string($payment->payment_date))
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y, H:i') }}
                            @else
                                {{ $payment->payment_date->format('d M Y, H:i') }}
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($payment->order->total_price, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @php
                            $statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';

                            if($payment->status == 'pending') {
                                $statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300';
                            } elseif($payment->status == 'completed') {
                                $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
                            } elseif($payment->status == 'cancelled') {
                                $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
                            } elseif($payment->status == 'failed') {
                                $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
                            }
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                            Detail
                        </a>

                        @if($payment->status == 'pending')
                        <div class="relative inline-block text-left ml-2" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 focus:outline-none z-10"
                                 role="menu"
                                 aria-orientation="vertical"
                                 aria-labelledby="menu-button">
                                <div class="py-1" role="none">
                                    <form action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST" class="block">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="text-left w-full px-4 py-2 text-sm text-green-700 dark:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                            <i class="fas fa-check mr-2"></i> Tandai Selesai
                                        </button>
                                    </form>
                                </div>
                                <div class="py-1" role="none">
                                    <form action="{{ route('admin.payments.updateStatus', $payment) }}" method="POST" class="block">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="text-left w-full px-4 py-2 text-sm text-red-700 dark:text-red-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                            <i class="fas fa-times mr-2"></i> Batalkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="py-8">
                            <i class="fas fa-search text-4xl mb-3 text-gray-400 dark:text-gray-600"></i>
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

<!-- Payment Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
    <!-- Total Payments -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-full p-3">
                <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pembayaran</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $payments->count() }}</p>
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
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $payments->where('status', 'completed')->count() }}
                </p>
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
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pembayaran Tertunda</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $payments->where('status', 'pending')->count() }}
                </p>
            </div>
        </div>
    </div>

    <!-- Cancelled/Failed Payments -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-500 rounded-full p-3">
                <i class="fas fa-times-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pembayaran Gagal/Batal</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $payments->whereIn('status', ['cancelled', 'failed'])->count() }}
                </p>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $payments->appends(request()->query())->links() }}
    </div>

    <!-- Export Buttons -->
    <div class="mt-6 flex justify-end">
        <a href="{{ route('admin.payments.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-file-excel mr-2"></i> Export Excel
        </a>
        <a href="{{ route('admin.payments.export-pdf', request()->query()) }}" class="ml-2 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-file-pdf mr-2"></i> Export PDF
        </a>
    </div>
@endsection

@section('scripts')
<script>
    // Date range picker initialization (if needed)
    document.addEventListener('DOMContentLoaded', function() {
        // Any JavaScript needed for this page

        // Flash message auto-hide
        setTimeout(function() {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.opacity = '0';
                setTimeout(function() {
                    flashMessage.style.display = 'none';
                }, 500);
            }
        }, 5000);
    });
</script>
@endsection
