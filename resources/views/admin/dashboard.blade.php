@extends('admin.layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Events Card -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-full p-3">
                <i class="fas fa-calendar-alt text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Acara</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalEvents }}</p>
            </div>
        </div>
    </div>

    <!-- Upcoming Events Card -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                <i class="fas fa-hourglass-half text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Acara Mendatang</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $upcomingEvents }}</p>
            </div>
        </div>
    </div>

    <!-- Categories Card -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-500 rounded-full p-3">
                <i class="fas fa-tags text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Kategori</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $categories }}</p>
            </div>
        </div>
    </div>

    <!-- Active Tickets Card -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-500 rounded-full p-3">
                <i class="fas fa-ticket-alt text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Tiket Aktif</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $activeTickets }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Events Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 flex justify-between items-center border-b">
        <h2 class="text-lg font-semibold text-gray-900">Acara Terbaru</h2>
        <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($recentEvents as $event)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $event->category->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $event->location }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $event->start_event->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('events.show', $event) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                        <a href="{{ route('events.edit', $event) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        Tidak ada acara terbaru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Stats Chart -->
<div class="mt-6 bg-white rounded-lg shadow p-4">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Kategori Acara</h2>
    <div id="category-chart" class="h-80"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryNames = @json($categoryNames ?? []);
        const eventCounts = @json($eventCounts ?? []);

        // Initialize the chart
        if (categoryNames.length > 0 && eventCounts.length > 0) {
            // Initialize the chart dengan data dari database
            var options = {
                chart: {
                    type: 'pie',
                    height: 350
                },
                series: eventCounts, // Data jumlah event per kategori dari database
                labels: categoryNames, // Nama-nama kategori dari database
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316', '#14B8A6', '#6366F1'],
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + ' event' + (value > 1 ? 's' : '');
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px'
                }
            };

            var chart = new ApexCharts(document.querySelector("#category-chart"), options);
            chart.render();
        } else {
            // Jika data tidak tersedia, tampilkan pesan kosong
            document.querySelector("#category-chart").innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-gray-500">Tidak ada data kategori untuk ditampilkan.</p></div>';
        }
    });
</script>
@endpush
@endsection
