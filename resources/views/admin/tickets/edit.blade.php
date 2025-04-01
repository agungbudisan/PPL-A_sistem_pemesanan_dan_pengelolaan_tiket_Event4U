@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Tiket: {{ $ticket->ticket_class }}</h1>
    <a href="{{ route('events.tickets.index', $ticket->event) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<!-- Event Info Card -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
    <div class="flex items-center">
        <div class="flex-shrink-0 mr-4">
            <img src="{{ asset('storage/' . $ticket->event->thumbnail) }}" alt="{{ $ticket->event->title }}" class="w-16 h-16 object-cover rounded-lg">
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $ticket->event->title }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ticket->event->start_event->format('d M Y, H:i') }} di {{ $ticket->event->location }}</p>
        </div>
    </div>
</div>

<!-- Ticket Sales Status -->
@php
    $ordersCount = $ticket->orders->count();
    $soldQuantity = $ticket->orders->sum('quantity');
    $hasOrders = $ordersCount > 0;
@endphp

@if($hasOrders)
<div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-600 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-400 dark:text-yellow-600"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700 dark:text-yellow-400">
                Tiket ini telah dibeli oleh {{ $ordersCount }} pembeli dengan total {{ $soldQuantity }} tiket terjual. Beberapa perubahan mungkin terbatas.
            </p>
        </div>
    </div>
</div>
@endif

<div
    x-data="{
        ticketClass: '{{ old('ticket_class', $ticket->ticket_class) }}',
        price: {{ old('price', $ticket->price) }},
        quota: {{ old('quota_avail', $ticket->quota_avail) }},
        description: `{{ old('description', $ticket->description) }}`,
        hasOrders: {{ $hasOrders ? 'true' : 'false' }},
        soldQuantity: {{ $soldQuantity }},

        formattedPrice() {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(this.price);
        },

        totalRevenue() {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(this.price * this.quota);
        }
    }"
    class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
>
    <div class="p-6">
        <form action="{{ route('tickets.update', $ticket) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jenis Tiket -->
                <div>
                    <label for="ticket_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Tiket <span class="text-red-500">*</span></label>
                    <input
                        x-model="ticketClass"
                        type="text"
                        name="ticket_class"
                        id="ticket_class"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Contoh: VIP, Regular, Early Bird, dll.</p>
                    @error('ticket_class')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                        </div>
                        <input
                            x-model="price"
                            type="number"
                            name="price"
                            id="price"
                            class="block w-full pl-10 pr-12 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            placeholder="0"
                            required
                            :readonly="hasOrders"
                        >
                    </div>
                    <p x-show="price > 0" class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="'Harga yang ditampilkan: ' + formattedPrice()"></p>
                    <p x-show="hasOrders" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Harga tidak dapat diubah karena tiket sudah terjual.</p>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kuota -->
                <div>
                    <label for="quota_avail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kuota Tiket <span class="text-red-500">*</span></label>
                    <input
                        x-model="quota"
                        type="number"
                        name="quota_avail"
                        id="quota_avail"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                        :min="soldQuantity"
                        required
                    >
                    <template x-if="hasOrders">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="'Kuota minimal: ' + soldQuantity + ' (tiket yang sudah terjual)'"></p>
                    </template>
                    <template x-if="!hasOrders">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah total tiket yang tersedia untuk jenis ini</p>
                    </template>
                    @error('quota_avail')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Total Pendapatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimasi Pendapatan</label>
                    <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <div class="flex flex-col gap-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Total pendapatan jika semua tiket terjual:
                            </p>
                            <p class="text-lg font-medium text-gray-900 dark:text-white" x-text="totalRevenue()"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi <span class="text-red-500">*</span></label>
                <textarea
                    x-model="description"
                    name="description"
                    id="description"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                    required
                ></textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jelaskan detail tentang apa yang didapatkan dengan tiket ini</p>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Detail Penjualan for Edit -->
            @if($hasOrders)
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ringkasan Penjualan</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $ordersCount }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tiket Terjual</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $soldQuantity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Penjualan</p>
                        <p class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($ticket->price * $soldQuantity, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sisa Tiket</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $ticket->quota_avail - $soldQuantity }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Preview Tiket -->
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Preview Tiket</h3>
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                    <div class="bg-white dark:bg-gray-800 p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="ticketClass || 'Nama Tiket'"></h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="(description || 'Deskripsi tiket akan ditampilkan di sini').substr(0, 60) + (description && description.length > 60 ? '...' : '')"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400" x-text="formattedPrice()"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="quota + ' tiket tersedia'"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->event->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->event->start_event->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <div class="bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200 rounded-full px-3 py-1 text-xs font-medium">
                                    <span x-text="ticketClass || 'Tiket'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informasi Penting</h3>
                <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>Tiket akan dijual pada periode:
                        {{ $ticket->event->start_sale->format('d M Y, H:i') }} -
                        {{ $ticket->event->end_sale->format('d M Y, H:i') }}
                    </li>
                    <li>Pastikan Anda mengatur kuota tiket dengan benar</li>
                    <li>Harga tiket tidak dapat diubah setelah ada pembelian</li>
                </ul>
            </div>

            <!-- Tombol Submit -->
            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <i class="fas fa-save mr-2"></i> Perbarui Tiket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
