<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 rounded-full bg-[#7B0015] flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Selamat datang, {{ Auth::user()->name }}!</h3>
                            <p class="text-gray-600">Terima kasih telah menggunakan layanan Event4U</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Orders Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Pemesanan</p>
                                @php
                                    $totalOrders = \App\Models\Order::where('user_id', Auth::id())->count();
                                @endphp
                                <p class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
                            </div>
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Acara Mendatang</p>
                                @php
                                    $upcomingEvents = \App\Models\Order::where('user_id', Auth::id())
                                        ->whereHas('ticket.event', function($query) {
                                            $query->where('start_event', '>', now());
                                        })
                                        ->count();
                                @endphp
                                <p class="text-2xl font-bold text-gray-900">{{ $upcomingEvents }}</p>
                            </div>
                            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-calendar text-green-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Tiket Aktif</p>
                                @php
                                    $activeTickets = \App\Models\Order::where('user_id', Auth::id())
                                        ->whereHas('payment', function($query) {
                                            $query->where('status', 'completed');
                                        })
                                        ->count();
                                @endphp
                                <p class="text-2xl font-bold text-gray-900">{{ $activeTickets }}</p>
                            </div>
                            <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-ticket-alt text-purple-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Pesanan Terbaru</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm text-[#7B0015] hover:underline">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="p-6">
                    @php
                        $recentOrders = \App\Models\Order::where('user_id', Auth::id())
                            ->with(['ticket.event', 'payment'])
                            ->latest()
                            ->take(3)
                            ->get();
                    @endphp

                    @if($recentOrders->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($recentOrders as $order)
                                <div class="py-4 flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $order->ticket->event->title ?? 'Acara' }}</p>
                                        <div class="text-sm text-gray-500">
                                            <span>{{ $order->ticket->ticket_class }}</span>
                                            <span class="mx-1">•</span>
                                            <span>{{ $order->quantity }}x tiket</span>
                                            <span class="mx-1">•</span>
                                            <span>
                                                @if(isset($order->payment) && $order->payment->status == 'completed')
                                                    <span class="text-green-600">Pembayaran Selesai</span>
                                                @elseif(isset($order->payment) && $order->payment->status == 'pending')
                                                    <span class="text-yellow-600">Menunggu Pembayaran</span>
                                                @else
                                                    <span class="text-gray-500">Belum Dibayar</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('orders.show', $order) }}" class="text-sm text-[#7B0015] hover:underline">Detail</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="h-20 w-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-ticket-alt text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500">Anda belum memiliki pesanan</p>
                            <a href="{{ route('events.index') }}" class="mt-2 inline-block px-4 py-2 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg">
                                Jelajahi Acara
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-medium text-gray-900">Acara yang Akan Datang</h3>
                </div>

                <div class="p-6">
                    @php
                        // Hapus where clause dengan status karena kolom tersebut tidak ada
                        $upcomingEvents = \App\Models\Event::where('start_event', '>', now())
                            ->take(3)
                            ->get();
                    @endphp

                    @if($upcomingEvents->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($upcomingEvents as $event)
                                <div class="border border-gray-200 rounded-lg overflow-hidden flex flex-col h-full">
                                    <div class="h-40 overflow-hidden">
                                        <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="p-4 flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ $event->title }}</h4>
                                        <div class="text-sm text-gray-500 space-y-1">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-alt text-[#7B0015] mr-2"></i>
                                                <span>{{ $event->start_event->format('d F Y') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt text-[#7B0015] mr-2"></i>
                                                <span>{{ $event->location }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4 border-t border-gray-200">
                                        <a href="{{ route('events.show', $event) }}" class="block w-full py-2 bg-[#7B0015] hover:bg-[#950019] text-white text-center font-medium rounded-lg">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Tidak ada acara mendatang saat ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
