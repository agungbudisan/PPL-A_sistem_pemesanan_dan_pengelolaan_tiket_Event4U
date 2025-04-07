<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Event 4 U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    @include('components.navbar')

    <main class="container mx-auto mb-16 mt-8 px-4 lg:px-6">
        <!-- Event Header with Banner -->
        <div class="relative rounded-xl overflow-hidden shadow-xl h-60 md:h-80 mb-6">
            @if($event->thumbnail)
                <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="w-full h-full object-cover" />
            @else
                <div class="w-full h-full bg-gradient-to-r from-[#7B0015] to-[#AF0020] flex items-center justify-center">
                    <i class="fas fa-calendar-day text-6xl text-white opacity-25"></i>
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end">
                <div class="p-6 text-white w-full">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-block bg-[#7B0015] text-white text-xs px-2 py-1 rounded-full mb-2">
                                {{ $event->category->name ?? 'Event' }}
                            </span>
                            <h1 class="text-3xl md:text-4xl font-bold">{{ $event->title }}</h1>
                        </div>
                        @php
                            $now = now();
                            $isSaleOpen = $now >= $event->start_sale && $now <= $event->end_sale;
                            $isUpcoming = $now < $event->start_event;
                            $isOngoing = $now >= $event->start_event && $now <= $event->end_event;
                            $isPast = $now > $event->end_event;
                        @endphp
                        <div>
                            @if($isUpcoming)
                                <span class="inline-block bg-blue-600 text-white text-sm px-3 py-1 rounded-lg">
                                    Akan Datang
                                </span>
                            @elseif($isOngoing)
                                <span class="inline-block bg-green-600 text-white text-sm px-3 py-1 rounded-lg">
                                    Sedang Berlangsung
                                </span>
                            @elseif($isPast)
                                <span class="inline-block bg-gray-600 text-white text-sm px-3 py-1 rounded-lg">
                                    Selesai
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column - Event Details -->
            <div class="md:col-span-1 space-y-4">
                <!-- Date and Location Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-[#7B0015] text-white p-4">
                        <h2 class="font-bold text-lg">Informasi Event</h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-start">
                            <div class="bg-red-100 p-2 rounded-lg text-[#7B0015] mr-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal & Waktu</p>
                                <p class="font-medium">{{ date('d F Y', strtotime($event->start_event)) }}</p>
                                <p class="text-sm">{{ date('H:i', strtotime($event->start_event)) }} - {{ date('H:i', strtotime($event->end_event)) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-red-100 p-2 rounded-lg text-[#7B0015] mr-3">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Lokasi</p>
                                <p class="font-medium">{{ $event->location }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-red-100 p-2 rounded-lg text-[#7B0015] mr-3">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Penjualan Tiket</p>
                                <p class="font-medium">
                                    {{ date('d F Y', strtotime($event->start_sale)) }} - {{ date('d F Y', strtotime($event->end_sale)) }}
                                </p>
                                @if($isSaleOpen)
                                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mt-1">
                                        Penjualan Dibuka
                                    </span>
                                @elseif($now < $event->start_sale)
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded mt-1">
                                        Penjualan Belum Dibuka
                                    </span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded mt-1">
                                        Penjualan Ditutup
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage Layout -->
                @if($event->stage_layout && $event->has_stage_layout)
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-[#7B0015] text-white p-4">
                        <h2 class="font-bold text-lg">Layout Venue</h2>
                    </div>
                    <div class="p-4">
                        <img src="{{ asset('storage/' . $event->stage_layout) }}" alt="Layout Venue" class="w-full h-auto rounded-lg" />
                    </div>
                </div>
                @endif

                <!-- Information Box -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-[#7B0015] text-white p-4">
                        <h2 class="font-bold text-lg">Informasi Pemesanan</h2>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Maksimum 5 tiket per jenis tiket dalam sekali pemesanan.
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                            Pembayaran harus dilakukan dalam waktu 1 jam setelah pemesanan.
                        </p>
                    </div>
                </div>

                <!-- Share Event -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-[#7B0015] text-white p-4">
                        <h2 class="font-bold text-lg">Bagikan Event</h2>
                    </div>
                    <div class="p-4 flex justify-center space-x-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-facebook-square text-2xl"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($event->title) }}" target="_blank" class="text-blue-400 hover:text-blue-600">
                            <i class="fab fa-twitter-square text-2xl"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($event->title . ' - ' . url()->current()) }}" target="_blank" class="text-green-600 hover:text-green-800">
                            <i class="fab fa-whatsapp-square text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column - Description and Tickets -->
            <div class="md:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-2xl font-bold mb-4">Deskripsi Event</h2>
                    <div class="prose prose-red max-w-none text-gray-700">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>

                <!-- Tickets Section -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold">Tiket</h2>

                    @if(!$isSaleOpen)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        @if($now < $event->start_sale)
                                            Penjualan tiket belum dimulai. Penjualan akan dibuka pada {{ date('d F Y, H:i', strtotime($event->start_sale)) }}.
                                        @else
                                            Penjualan tiket sudah ditutup.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @foreach($event->tickets as $ticket)
                        <div class="bg-white border-2 {{ $isSaleOpen ? 'border-red-700' : 'border-gray-300' }} rounded-xl overflow-hidden shadow-md">
                            <div class="p-6 grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-4">
                                <div>
                                    <h3 class="text-xl font-bold {{ $isSaleOpen ? 'text-[#7B0015]' : 'text-gray-600' }}">{{ $ticket->ticket_class }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $ticket->description ?? 'Tiket ' . $ticket->ticket_class }}</p>
                                    <div class="mt-2 flex items-center">
                                        @if($ticket->quota_avail > 0 && $isSaleOpen)
                                            <span class="flex items-center space-x-1 text-sm text-green-600">
                                                <i class="fas fa-circle text-xs"></i>
                                                <span>{{ $ticket->quota_avail }} tiket tersedia</span>
                                            </span>
                                        @elseif(!$isSaleOpen)
                                            <span class="flex items-center space-x-1 text-sm text-gray-500">
                                                <i class="fas fa-lock text-xs"></i>
                                                <span>Penjualan tidak tersedia</span>
                                            </span>
                                        @else
                                            <span class="flex items-center space-x-1 text-sm text-yellow-600">
                                                <i class="fas fa-circle text-xs"></i>
                                                <span>Tiket habis</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col md:items-end justify-between">
                                    <div class="text-right">
                                        <span class="text-lg font-bold {{ $isSaleOpen ? 'text-gray-900' : 'text-gray-500' }}">
                                            Rp{{ number_format($ticket->price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center mt-2 md:mt-0 {{ $isSaleOpen ? '' : 'opacity-50' }}">
                                        <button type="button"
                                            onclick="decrease('{{ $ticket->id }}')"
                                            class="w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 rounded-l {{ (!$isSaleOpen || $ticket->quota_avail < 1) ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-300' }}"
                                            {{ (!$isSaleOpen || $ticket->quota_avail < 1) ? 'disabled' : '' }}>
                                            <i class="fas fa-minus"></i>
                                        </button>

                                        <input type="number"
                                            id="qty-{{ $ticket->id }}"
                                            data-ticket-id="{{ $ticket->id }}"
                                            data-name="{{ $ticket->ticket_class }}"
                                            data-price="{{ $ticket->price }}"
                                            data-stock="{{ $ticket->quota_avail }}"
                                            min="0"
                                            max="{{ min(5, $ticket->quota_avail) }}"
                                            value="0"
                                            oninput="validateInput(this)"
                                            class="ticket-qty w-14 h-8 text-center outline-none border border-gray-200 px-1"
                                            {{ (!$isSaleOpen || $ticket->quota_avail < 1) ? 'disabled' : '' }}>

                                        <button type="button"
                                            onclick="increase('{{ $ticket->id }}')"
                                            class="w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 rounded-r {{ (!$isSaleOpen || $ticket->quota_avail < 1) ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-300' }}"
                                            {{ (!$isSaleOpen || $ticket->quota_avail < 1) ? 'disabled' : '' }}>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="bg-white p-6 rounded-xl shadow-md space-y-4 sticky bottom-0 md:relative border-t-2 border-[#7B0015]">
                    <h2 class="text-xl font-bold mb-2">Ringkasan Pesanan</h2>
                    <ul id="order-summary" class="text-gray-800 text-sm space-y-2">
                        <li class="text-gray-500">Belum ada tiket dipilih.</li>
                    </ul>
                    <div class="pt-4 border-t border-gray-200">
                        <p class="font-semibold text-lg flex justify-between">
                            <span>Total:</span>
                            <span id="total-price" class="text-[#7B0015]">Rp0</span>
                        </p>

                        <!-- Form untuk pemilihan tiket -->
                        <form action="{{ route('orders.create', ['ticket' => 0]) }}" method="GET" id="order-form" onsubmit="return prepareOrderData()">
                            <input type="hidden" name="tickets" id="tickets-data">
                            <button type="submit"
                                class="w-full mt-4 bg-[#7B0015] hover:bg-[#950019] text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#7B0015] disabled:hover:scale-100"
                                {{ !$isSaleOpen ? 'disabled' : '' }}>
                                {{ $isSaleOpen ? 'Pesan Tiket' : 'Penjualan Tidak Tersedia' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('components.footer')

    <script>
        function increase(id) {
            const input = document.getElementById('qty-' + id);
            const max = Math.min(5, parseInt(input.dataset.stock)); // Maksimum 5 atau sesuai stock
            let val = parseInt(input.value);

            if (val < max) {
                input.value = val + 1;
                updateSummary();
            }
        }

        function decrease(id) {
            const input = document.getElementById('qty-' + id);
            let val = parseInt(input.value);

            if (val > 0) {
                input.value = val - 1;
                updateSummary();
            }
        }

        function validateInput(input) {
            // Memastikan hanya angka positif dan maksimum 5
            let val = parseInt(input.value) || 0;
            const max = Math.min(5, parseInt(input.dataset.stock));

            // Batasi nilai antara 0 dan max
            val = Math.max(0, Math.min(val, max));

            // Update nilai
            input.value = val;
            updateSummary();
        }

        function updateSummary() {
            const items = document.querySelectorAll('.ticket-qty');
            const summary = document.getElementById('order-summary');
            const totalPriceEl = document.getElementById('total-price');
            let total = 0;
            let output = '';
            let hasOrder = false;

            items.forEach(input => {
                const qty = parseInt(input.value);
                if (qty > 0) {
                    hasOrder = true;
                    const name = input.dataset.name;
                    const price = parseInt(input.dataset.price);
                    const subtotal = price * qty;
                    total += subtotal;

                    output += `<li class="flex justify-between"><span>${name} × ${qty}</span> <span>Rp${subtotal.toLocaleString('id-ID')}</span></li>`;
                }
            });

            summary.innerHTML = hasOrder ? output : '<li class="text-gray-500">Belum ada tiket dipilih.</li>';
            totalPriceEl.textContent = 'Rp' + total.toLocaleString('id-ID');
        }

        function prepareOrderData() {
            const items = document.querySelectorAll('.ticket-qty');
            const data = [];
            let found = false;
            let firstTicketId = 0;

            items.forEach(input => {
                const qty = parseInt(input.value);
                if (qty > 0) {
                    found = true;
                    const ticketId = input.dataset.ticketId;
                    if (firstTicketId === 0) {
                        firstTicketId = ticketId;
                    }
                    data.push({
                        ticket_id: ticketId,
                        quantity: qty
                    });
                }
            });

            if (!found) {
                alert('Silakan pilih minimal 1 tiket.');
                return false;
            }

            // Update route dengan ID tiket pertama
            const form = document.getElementById('order-form');

            // Tentukan route berdasarkan status login
            @auth
                form.action = "{{ route('orders.create', ':ticket_id') }}".replace(':ticket_id', firstTicketId);
            @else
                form.action = "{{ route('guest.orders.create', ':ticket_id') }}".replace(':ticket_id', firstTicketId);
            @endauth

            document.getElementById('tickets-data').value = JSON.stringify(data);
            return true;
        }

        // Initialize when document is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set max values for all ticket inputs
            document.querySelectorAll('.ticket-qty').forEach(input => {
                const max = Math.min(5, parseInt(input.dataset.stock));
                input.setAttribute('max', max);
            });
        });
    </script>

</body>
</html>
