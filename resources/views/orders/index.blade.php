<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Event 4 U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    @include('components.navbar')

    <main class="container mx-auto mb-16 mt-8 px-4 lg:px-6">
        <div class="max-w-5xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Pesanan Saya</h1>
                <a href="{{ route('events.index') }}" class="py-2 px-4 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-ticket-alt mr-2"></i> Cari Event
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter dan Sorting -->
            <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Event</label>
                        <input type="text" id="search" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B0015]" placeholder="Masukkan nama event" value="{{ request('search') }}">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B0015]">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Sukses</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Urutkan</label>
                        <select id="sort" name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B0015]">
                            <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-black text-white font-medium rounded-md transition duration-300">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            @if($orders->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-ticket-alt text-gray-400 text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-700 mb-2">Belum ada pesanan</h2>
                    <p class="text-gray-500 mb-6">Anda belum melakukan pemesanan tiket event apapun.</p>
                    <a href="{{ route('events.index') }}" class="inline-block py-2 px-4 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg transition duration-300">
                        Jelajahi Event
                    </a>
                </div>
            @else
                <!-- Daftar Pesanan -->
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                            <div class="border-b border-gray-200">
                                <div class="flex justify-between items-center p-4">
                                    <span class="text-sm text-gray-500">ID Pesanan: #{{ $order->id }}</span>

                                    @php
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Belum Dibayar';

                                        if($order->payment) {
                                            switch($order->payment->status) {
                                                case 'completed':
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'Pembayaran Sukses';
                                                    break;
                                                case 'pending':
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Menunggu Pembayaran';
                                                    break;
                                                case 'failed':
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                    $statusText = 'Pembayaran Gagal';
                                                    break;
                                                case 'cancelled':
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusText = 'Dibatalkan';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusText = ucfirst($order->payment->status);
                                            }
                                        }
                                    @endphp

                                    <span class="px-3 py-1 text-xs rounded-full {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="md:flex items-start">
                                    <!-- Event Info -->
                                    <div class="md:flex-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $order->ticket->event->title }}</h3>
                                        <div class="flex flex-wrap gap-3 text-sm text-gray-500 mb-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-alt text-[#7B0015] mr-1"></i>
                                                <span>{{ $order->ticket->event->start_event->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-clock text-[#7B0015] mr-1"></i>
                                                <span>{{ $order->ticket->event->start_event->format('H:i') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt text-[#7B0015] mr-1"></i>
                                                <span>{{ $order->ticket->event->location }}</span>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                            <div class="flex justify-between text-sm mb-1">
                                                <span>{{ $order->ticket->ticket_class }}</span>
                                                <span>{{ $order->quantity }} × Rp {{ number_format($order->ticket->price, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between font-medium">
                                                <span>Total</span>
                                                <span class="text-[#7B0015]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        <div class="text-sm text-gray-500">
                                            <p>Dipesan pada: {{ $order->order_date->format('d M Y, H:i') }}</p>
                                            @if($order->payment)
                                                <p>Metode pembayaran:
                                                    @if($order->payment->method === 'transfer')
                                                        Transfer Bank
                                                    @elseif($order->payment->method === 'ewallet')
                                                        E-Wallet
                                                    @elseif($order->payment->method === 'credit_card')
                                                        Kartu Kredit
                                                    @else
                                                        {{ $order->payment->method }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="md:ml-4 mt-4 md:mt-0 flex md:flex-col gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 bg-gray-800 hover:bg-black text-white text-sm font-medium rounded-lg text-center w-full whitespace-nowrap">
                                            Detail Pesanan
                                        </a>

                                        @if($order->payment && $order->payment->status === 'completed')
                                            <a href="{{ route('orders.download-ticket', $order) }}" class="px-4 py-2 bg-[#7B0015] hover:bg-[#950019] text-white text-sm font-medium rounded-lg text-center w-full flex items-center justify-center whitespace-nowrap">
                                                <i class="fas fa-download mr-1"></i> E-Ticket
                                            </a>
                                        @elseif(!$order->payment || $order->payment->status === 'pending')
                                            @php
                                                $orderTime = $order->order_date;
                                                $now = now();
                                                $diffInHours = $now->diffInHours($orderTime);
                                            @endphp

                                            @if($diffInHours < 1)
                                                <a href="{{ route('payments.create', $order) }}" class="px-4 py-2 bg-[#7B0015] hover:bg-[#950019] text-white text-sm font-medium rounded-lg text-center w-full whitespace-nowrap">
                                                    Bayar Sekarang
                                                </a>
                                            @else
                                                <span class="px-4 py-2 bg-gray-200 text-gray-500 text-sm font-medium rounded-lg text-center w-full whitespace-nowrap">
                                                    Pembayaran Kedaluwarsa
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </main>

    @include('components.footer')
</body>
</html>
