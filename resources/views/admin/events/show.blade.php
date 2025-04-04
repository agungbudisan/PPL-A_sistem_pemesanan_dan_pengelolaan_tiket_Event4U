@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Detail Acara: {{ $event->title }}</h1>
    <div class="flex space-x-2">
        <a href="{{ route('admin.tickets.index', $event) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-ticket-alt mr-2"></i> Kelola Tiket
        </a>
        <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Thumbnail dan Info Dasar -->
            <div class="md:col-span-1">
                <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="w-full h-auto rounded-lg shadow">

                <div class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Info Acara</h3>

                    <div class="space-y-2">
                        <div class="flex items-start">
                            <i class="fas fa-tag w-5 h-5 text-indigo-500 dark:text-indigo-400 mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kategori</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->category->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt w-5 h-5 text-indigo-500 dark:text-indigo-400 mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Lokasi</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->location }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt w-5 h-5 text-indigo-500 dark:text-indigo-400 mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Acara</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->start_event->format('d M Y, H:i') }} - {{ $event->end_event->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-ticket-alt w-5 h-5 text-indigo-500 dark:text-indigo-400 mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Penjualan Tiket</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->start_sale->format('d M Y, H:i') }} - {{ $event->end_sale->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-user w-5 h-5 text-indigo-500 dark:text-indigo-400 mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->admin->name ?? 'Admin' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <i class="fas fa-clock w-5 h-5 text-indigo-500 dark:text-indigo-400 mr-2 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Terakhir Diperbarui</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi dan Detail Lainnya -->
            <div class="md:col-span-2">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ $event->title }}</h2>

                <!-- Status Acara -->
                @php
                    $now = now();
                    $status = 'past';
                    $statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                    $statusText = 'Selesai';

                    if ($event->start_event > $now) {
                        $status = 'upcoming';
                        $statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
                        $statusText = 'Mendatang';
                    } elseif ($event->end_event > $now) {
                        $status = 'ongoing';
                        $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
                        $statusText = 'Berlangsung';
                    }
                @endphp
                <div class="mb-4">
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </div>

                <!-- Deskripsi -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Deskripsi</h3>
                    <div class="prose max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>

                <!-- Stage Layout -->
                @if($event->stage_layout)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-lg mb-2 text-gray-900 dark:text-white">Layout Panggung</h3>
                    <div class="flex flex-col items-center">
                        <img src="{{ asset('storage/' . $event->stage_layout) }}" alt="Layout Panggung" class="max-h-96 w-auto rounded-lg shadow">
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Layout panggung acara akan ditampilkan kepada pembeli tiket</p>
                    </div>
                </div>
                @endif

                <!-- Tiket -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">Tiket Tersedia</h3>
                        <a href="{{ route('admin.tickets.create', $event) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-1"></i> Tambah Tiket
                        </a>
                    </div>

                    @if($event->tickets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Tiket</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kuota</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($event->tickets as $ticket)
                                    @php
                                        $sold = $ticket->orders->sum('quantity');
                                        $percentage = $ticket->quota_avail > 0 ? ($sold / $ticket->quota_avail) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->ticket_class }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($ticket->description, 50) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">Rp {{ number_format($ticket->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">{{ $ticket->quota_avail }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $sold }}/{{ $ticket->quota_avail }} terjual</span>
                                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mt-1">
                                                    <div class="bg-indigo-600 dark:bg-indigo-400 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus tiket ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-4 text-gray-500 dark:text-gray-400">
                            <p>Belum ada tiket tersedia untuk acara ini.</p>
                            <p class="mt-2 text-sm">Klik tombol "Tambah Tiket" untuk membuat tiket baru.</p>
                        </div>
                    @endif
                </div>

                <!-- Statistik Penjualan -->
                @if($event->tickets->count() > 0)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-lg mb-4 text-gray-900 dark:text-white">Statistik Penjualan Cepat</h3>
                    @php
                        $totalSales = 0;
                        $totalTickets = 0;
                        $totalSold = 0;

                        foreach($event->tickets as $ticket) {
                            $sold = $ticket->orders->sum('quantity');
                            $totalSales += $ticket->price * $sold;
                            $totalTickets += $ticket->quota_avail;
                            $totalSold += $sold;
                        }

                        $percentageSold = $totalTickets > 0 ? ($totalSold / $totalTickets) * 100 : 0;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tiket Terjual</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $totalSold }}/{{ $totalTickets }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Persentase Terjual</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($percentageSold, 1) }}%</p>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.analytics', ['eventId' => $event->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-chart-line mr-2"></i> Lihat Analitik Lengkap
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
