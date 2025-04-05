@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Kelola Acara</h1>
    <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <i class="fas fa-plus mr-2"></i> Tambah Acara
    </a>
</div>

<!-- Alert Success/Error -->
{{-- @if(session('success'))
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 dark:bg-green-800/20 dark:text-green-400" role="alert">
        <p>{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 dark:bg-red-800/20 dark:text-red-400" role="alert">
        <p>{{ session('error') }}</p>
    </div>
@endif --}}

<!-- Filter and Search -->
<form action="{{ route('admin.events.index') }}" method="GET" class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Acara</label>
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
                    placeholder="Cari berdasarkan judul, lokasi..."
                >
            </div>
        </div>
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
            <select
                name="category_id"
                id="category_id"
                class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm rounded-md bg-white dark:bg-gray-700 dark:text-white"
            >
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Mendatang</option>
                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Sedang Berlangsung</option>
                <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            @if(request('search') || request('category_id') || request('status'))
                <a href="{{ route('admin.events.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-times mr-2"></i> Reset
                </a>
            @endif
        </div>
    </div>

    <!-- Results summary -->
    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
        <p>Menampilkan {{ $events->count() }} acara</p>
    </div>
</form>

<!-- Events Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lokasi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Acara</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tiket</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($events as $index => $event)
                <tr class="{{ $index % 2 == 0 ? '' : 'bg-gray-50 dark:bg-gray-700' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($event->thumbnail)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $event->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $event->category->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $event->location }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $event->start_event->format('d M Y, H:i') }}
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            s/d {{ $event->end_event->format('d M Y, H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $now = now();
                            $class = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            $text = 'Selesai';

                            if ($event->start_event > $now) {
                                $class = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
                                $text = 'Mendatang';
                            } elseif ($event->end_event > $now) {
                                $class = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                                $text = 'Berlangsung';
                            }

                            // Status penjualan
                            $saleStatus = '';
                            $saleClass = '';

                            if ($now < $event->start_sale) {
                                $saleStatus = 'Penjualan belum dimulai';
                                $saleClass = 'text-yellow-600 dark:text-yellow-400';
                            } elseif ($now >= $event->start_sale && $now <= $event->end_sale) {
                                $saleStatus = 'Penjualan dibuka';
                                $saleClass = 'text-green-600 dark:text-green-400';
                            } else {
                                $saleStatus = 'Penjualan ditutup';
                                $saleClass = 'text-red-600 dark:text-red-400';
                            }
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">
                            {{ $text }}
                        </span>
                        <div class="mt-1 text-xs {{ $saleClass }}">
                            {{ $saleStatus }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium">{{ $event->tickets->count() }}</span> Jenis
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            @php
                                $totalSold = 0;
                                foreach ($event->tickets as $ticket) {
                                    $totalSold += $ticket->orders->sum('quantity');
                                }
                            @endphp
                            {{ $totalSold }} Terjual
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.tickets.index', $event) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Kelola Tiket">
                                <i class="fas fa-ticket-alt"></i>
                            </a>
                            <a href="{{ route('admin.analytics', $event->id) }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" title="Analytics">
                                <i class="fas fa-chart-line"></i>
                            </a>
                            <a href="{{ route('admin.events.show', $event) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.events.edit', $event) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus acara ini? Semua tiket dan order terkait juga akan terhapus.')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="py-8">
                            <i class="fas fa-search text-4xl mb-3 text-gray-400 dark:text-gray-600"></i>
                            <p class="text-lg font-medium">Tidak ada acara yang ditemukan</p>
                            <p class="text-sm mt-1">Coba ubah filter pencarian atau tambahkan acara baru</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
