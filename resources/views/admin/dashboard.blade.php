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
        // Sample data for the chart
        // In a real application, you'd pass this data from the controller
        const categories = @json($categories ?? []);
        const eventCounts = @json($eventCounts ?? []); // You need to add this to your controller

        // Initialize the chart
        var options = {
            chart: {
                type: 'pie',
                height: 350
            },
            series: [44, 55, 13, 43, 22], // This should be replaced with real data
            labels: ['Category 1', 'Category 2', 'Category 3', 'Category 4', 'Category 5'], // This should be replaced with real data
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
            }]
        };

        var chart = new ApexCharts(document.querySelector("#category-chart"), options);
        chart.render();
    });
</script>
@endpush
@endsection
