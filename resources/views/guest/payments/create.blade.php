<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - {{ $order->ticket->event->title }} - Event 4 U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Menghilangkan panah atas/bawah pada input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Untuk Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    @include('components.navbar')

    <main class="container mx-auto mb-16 mt-8 px-4 lg:px-6">
        <div class="max-w-3xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex items-center text-sm text-gray-500 mb-6">
                <a href="{{ route('welcome') }}" class="hover:text-[#7B0015]">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('events.index') }}" class="hover:text-[#7B0015]">Events</a>
                <span class="mx-2">/</span>
                <a href="{{ route('events.show', $order->ticket->event) }}" class="hover:text-[#7B0015]">{{ $order->ticket->event->title }}</a>
                <span class="mx-2">/</span>
                <span class="text-gray-700">Pembayaran</span>
            </nav>

            @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            <!-- Header Pembayaran -->
            <div class="bg-gradient-to-r from-[#7B0015] to-[#AF0020] rounded-t-xl p-6 text-white">
                <h1 class="text-2xl font-bold mb-1">Pembayaran</h1>
                <p>{{ $order->ticket->event->title }}</p>
                <p class="text-sm mt-2">Nomor Pesanan: {{ $order->reference }}</p>
            </div>

            <!-- Form Pembayaran -->
            <div class="bg-white rounded-b-xl shadow-md p-6">
                <!-- Countdown Timer -->
                <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-md" x-data="countdown()">
                    <div class="flex items-start">
                        <div class="text-yellow-500 mr-2 mt-1">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-yellow-700">Selesaikan pembayaran Anda dalam:</p>
                            <p class="text-lg font-bold" x-text="timer"></p>
                        </div>
                    </div>
                </div>

                <!-- Guest Information Summary -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h2 class="font-semibold mb-3">Informasi Pengguna</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Nama:</p>
                            <p class="font-medium">{{ $order->guest_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Email:</p>
                            <p class="font-medium">{{ $order->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">No. Telepon:</p>
                            <p class="font-medium">{{ $order->guest_phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h2 class="font-semibold mb-4">Ringkasan Pesanan</h2>
                    <div class="mb-3">
                        <p class="font-medium">{{ $order->ticket->event->title }}</p>
                        <p class="text-sm text-gray-600">{{ $order->ticket->event->start_event->format('d F Y, H:i') }}</p>
                        <p class="text-sm text-gray-600">{{ $order->ticket->event->location }}</p>
                    </div>
                    <div class="mb-3">
                        <div class="flex justify-between text-sm py-2 border-b border-gray-200">
                            <span>{{ $order->ticket->ticket_class }}</span>
                            <span>{{ $order->quantity }} × Rp {{ number_format($order->ticket->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold py-2">
                            <span>Total</span>
                            <span class="text-[#7B0015]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('guest.payments.store', $order->reference) }}" method="POST" x-data="{ paymentMethod: 'transfer' }">
                    @csrf

                    <div class="mb-4">
                        <label for="guest_email" class="block text-gray-700 font-medium mb-2">Konfirmasi Email</label>
                        <input type="email" name="guest_email" id="guest_email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7B0015]" value="{{ $order->email }}" required>
                        <p class="text-sm text-gray-500 mt-1">E-ticket akan dikirim ke email ini</p>
                        @error('guest_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Metode Pembayaran</label>

                        <!-- Bank Transfer -->
                        <div class="mb-3">
                            <label class="block p-4 border rounded-md cursor-pointer" :class="paymentMethod === 'transfer' ? 'border-[#7B0015] bg-red-50' : 'border-gray-300'">
                                <div class="flex items-start">
                                    <input type="radio" name="method" value="transfer" class="mt-1 mr-3" x-model="paymentMethod">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-university text-[#7B0015]"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold">Transfer Bank</p>
                                                <p class="text-sm text-gray-600">BCA, BNI, Mandiri, BRI</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 ml-12 text-sm" x-show="paymentMethod === 'transfer'">
                                            <div class="p-3 bg-gray-50 rounded-md">
                                                <p class="font-semibold">Instruksi Pembayaran:</p>
                                                <ol class="mt-2 ml-4 list-decimal text-gray-700">
                                                    <li class="mb-1">Transfer ke rekening <span class="font-medium">1234567890</span> a.n. Event 4 U</li>
                                                    <li class="mb-1">Jumlah transfer harus sama persis dengan total pembayaran</li>
                                                    <li>Sertakan kode pesanan {{ $order->reference }} pada keterangan transfer</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- E-Wallet -->
                        <div class="mb-3">
                            <label class="block p-4 border rounded-md cursor-pointer" :class="paymentMethod === 'ewallet' ? 'border-[#7B0015] bg-red-50' : 'border-gray-300'">
                                <div class="flex items-start">
                                    <input type="radio" name="method" value="ewallet" class="mt-1 mr-3" x-model="paymentMethod">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-wallet text-[#7B0015]"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold">E-Wallet</p>
                                                <p class="text-sm text-gray-600">GoPay, OVO, DANA, LinkAja</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 ml-12 text-sm" x-show="paymentMethod === 'ewallet'">
                                            <div class="p-3 bg-gray-50 rounded-md">
                                                <p class="font-semibold">Instruksi Pembayaran:</p>
                                                <ol class="mt-2 ml-4 list-decimal text-gray-700">
                                                    <li class="mb-1">Scan QR Code yang akan ditampilkan setelah konfirmasi</li>
                                                    <li class="mb-1">Pastikan nominal pembayaran sesuai</li>
                                                    <li>Konfirmasi pembayaran otomatis setelah pembayaran berhasil</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Credit Card -->
                        <div class="mb-3">
                            <label class="block p-4 border rounded-md cursor-pointer" :class="paymentMethod === 'credit_card' ? 'border-[#7B0015] bg-red-50' : 'border-gray-300'">
                                <div class="flex items-start">
                                    <input type="radio" name="method" value="credit_card" class="mt-1 mr-3" x-model="paymentMethod">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-credit-card text-[#7B0015]"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold">Kartu Kredit</p>
                                                <p class="text-sm text-gray-600">Visa, Mastercard, JCB</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 ml-12 text-sm" x-show="paymentMethod === 'credit_card'">
                                            <div class="p-3 bg-gray-50 rounded-md">
                                                <p class="font-semibold">Informasi:</p>
                                                <ul class="mt-2 ml-4 list-disc text-gray-700">
                                                    <li class="mb-1">Pembayaran diproses melalui gateway pembayaran aman</li>
                                                    <li class="mb-1">Anda akan diarahkan ke halaman pembayaran setelah mengklik "Bayar Sekarang"</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        @error('method')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-gray-100 p-4 rounded-lg mb-6">
                        <div class="flex items-start">
                            <div class="text-[#7B0015] mr-2 mt-1">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <p class="text-sm text-gray-600 flex-1">
                                Pembayaran harus dilakukan dalam waktu 1 jam. E-ticket akan dikirim ke email Anda setelah pembayaran berhasil dikonfirmasi.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full py-3 bg-[#7B0015] hover:bg-[#950019] text-white font-medium rounded-lg shadow-md transition duration-300">
                            Bayar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @include('components.footer')

    <script>
        function countdown() {
            return {
                timer: '59:59',
                startCountdown() {
                    let minutes = 59;
                    let seconds = 59;

                    const interval = setInterval(() => {
                        seconds--;

                        if (seconds < 0) {
                            minutes--;
                            seconds = 59;
                        }

                        if (minutes < 0) {
                            clearInterval(interval);
                            // Redirect to expired page or show message
                            window.location.href = "{{ route('events.show', $order->ticket->event) }}";
                            return;
                        }

                        this.timer = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    }, 1000);
                },
                init() {
                    this.startCountdown();
                }
            }
        }
    </script>
</body>
</html>
