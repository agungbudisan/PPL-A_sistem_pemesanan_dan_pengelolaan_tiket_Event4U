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
        <section class="max-w-6xl mx-auto my-8 p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <div class="aspect-[16/9]">
                    <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="rounded shadow-md w-full h-auto object-cover" />
                </div>
                <div class="mt-4">
                    <h2 class="font-bold text-lg">{{ $event->title }}</h2>
                    <p><i class="fas fa-calendar-alt mr-2 text-[#7B0015]"></i>{{ date('d F Y', strtotime($event->start_event)) }}</p>
                    <p><i class="fas fa-map-marker-alt mr-2 text-[#7B0015]"></i>{{ date('H:i', strtotime($event->start_event)) }}</p>
                    <p><i class="fas fa-clock mr-2 text-[#7B0015]"></i>{{ $event->location }}</p>
                </div>
            </div>

            <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-md space-y-4">
                <h1 class="text-3xl font-bold mb-4">Deskripsi Event</h1>
                <p class="text-gray-700 mb-4">{{ $event->description }}</p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto mt-10 grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6 items-start">
            <div class="space-y-6">
                @foreach($event->tickets as $ticket)
                    <div class="bg-red-700 text-white p-6 rounded shadow-md space-y-4">
                        <div>
                            <h3 class="text-xl font-bold">{{ $ticket->ticket_class }}</h3>
                            <p class="text-sm">Deskripsi: {{ $ticket->description ?? 'Tiket ' . $ticket->ticket_class }}</p>
                        </div>
                        <p class="font-semibold">Harga: Rp{{ number_format($ticket->price, 0, ',', '.') }}</p>

                        <div class="flex items-center justify-between">
                            <span class="flex items-center space-x-1">
                                @if($ticket->quota_avail > 0)
                                    <svg class="h-5 w-5" fill="white" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
                                    <span>{{ $ticket->quota_avail }} tersedia</span>
                                @else
                                    <svg class="h-5 w-5 text-yellow-300" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
                                    <span class="text-yellow-300 font-semibold">Tiket habis</span>
                                @endif
                            </span>

                            <div class="flex items-center bg-white text-red-700 rounded">
                                <button type="button"
                                    onclick="decrease('{{ $ticket->id }}')"
                                    class="px-3 py-1 font-bold {{ $ticket->quota_avail < 1 ? 'cursor-not-allowed text-red-300' : '' }}"
                                    {{ $ticket->quota_avail < 1 ? 'disabled' : '' }}>−</button>

                                <input type="number"
                                    id="qty-{{ $ticket->id }}"
                                    data-ticket-id="{{ $ticket->id }}"
                                    data-name="{{ $ticket->ticket_class }}"
                                    data-price="{{ $ticket->price }}"
                                    data-stock="{{ $ticket->quota_avail }}"
                                    value="0"
                                    class="ticket-qty w-16 text-center outline-none border-none bg-white text-red-700"
                                    readonly>

                                <button type="button"
                                    onclick="increase('{{ $ticket->id }}')"
                                    class="px-3 py-1 font-bold {{ $ticket->quota_avail < 1 ? 'text-red-400 cursor-not-allowed' : '' }}"
                                    {{ $ticket->quota_avail < 1 ? 'disabled' : '' }}>+</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md space-y-4">
                <h2 class="text-2xl font-bold mb-2">Ringkasan Pesanan</h2>
                <ul id="order-summary" class="text-gray-800 text-sm space-y-2">
                    <li class="text-gray-500">Belum ada tiket dipilih.</li>
                </ul>
                <p class="font-semibold text-lg mt-2">Total: <span id="total-price" class="text-[#7B0015]">Rp0</span></p>
                <form action="{{ route('guest.orders.store', $event->id) }}" method="POST" onsubmit="return prepareOrderData()">
                    @csrf
                    <input type="hidden" name="tickets" id="tickets-data">
                    <button type="submit" class="w-full bg-[#7B0015] hover:bg-[#950019] text-white font-bold py-3 px-6 rounded-full transition-all transform hover:scale-105 shadow-lg mt-4">
                        Pesan Tiket
                    </button>
                </form>
            </div>
        </section>
    </main>

    @include('components.footer')

    <script>
        function increase(id) {
            const input = document.getElementById('qty-' + id);
            const max = parseInt(input.dataset.stock);
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

                    output += `<li>${name} × ${qty} = Rp${subtotal.toLocaleString('id-ID')}</li>`;
                }
            });

            summary.innerHTML = hasOrder ? output : '<li class="text-gray-500">Belum ada tiket dipilih.</li>';
            totalPriceEl.textContent = 'Rp' + total.toLocaleString('id-ID');
        }

        function prepareOrderData() {
            const items = document.querySelectorAll('.ticket-qty');
            const data = [];

            items.forEach(input => {
                const qty = parseInt(input.value);
                if (qty > 0) {
                    data.push({
                        ticket_id: input.dataset.ticketId,
                        quantity: qty
                    });
                }
            });

            if (data.length === 0) {
                alert('Silakan pilih minimal 1 tiket.');
                return false;
            }

            document.getElementById('tickets-data').value = JSON.stringify(data);
            return true;
        }
    </script>

</body>
</html>
