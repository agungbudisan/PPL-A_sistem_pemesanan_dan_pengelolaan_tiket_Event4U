{{-- filepath: c:\FERA\Semester 6\PPL\PPL-A_sistem_pemesanan_dan_pengelolaan_tiket_Event4U\resources\views\events\index.blade.php --}}

<x-guest-layout>
    <x-slot:title>Daftar Event</x-slot:title>

    <main class="container mx-auto mb-32 mt-16 px-8 lg:px-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Daftar Event</h1>

        @if ($events->isEmpty())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded text-center">
                <p class="text-yellow-700">Tidak ada event yang tersedia saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($events as $event)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <!-- Thumbnail -->
                        @if($event->thumbnail)
                            <div class="h-48 bg-gray-100 overflow-hidden relative">
                                <img src="{{ asset('storage/' . $event->thumbnail) }}"
                                    alt="{{ $event->title }}"
                                    class="object-cover w-full h-full">
                                <button type="button"
                                        onclick="openFullImage('{{ asset('storage/' . $event->thumbnail) }}')"
                                        class="absolute bottom-2 right-2 bg-black/50 hover:bg-black/70 text-white text-xs px-2 py-1 rounded cursor-pointer transition">
                                    <i class="fas fa-expand-alt"></i> Lihat
                                </button>
                            </div>
                        @endif

                        <!-- Content -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded">
                                    {{ $event->category->name ?? 'Event' }}
                                </span>

                                @php
                                    $now = now();
                                    $isUpcoming = $now < $event->start_event;
                                    $isOngoing = $now >= $event->start_event && $now <= $event->end_event;
                                    $isPast = $now > $event->end_event;
                                @endphp

                                <span class="inline-block
                                    {{ $isUpcoming ? 'bg-blue-600' : ($isOngoing ? 'bg-green-600' : 'bg-gray-600') }}
                                    text-white text-xs px-2 py-1 rounded">
                                    {{ $isUpcoming ? 'Akan Datang' : ($isOngoing ? 'Sedang Berlangsung' : 'Selesai') }}
                                </span>
                            </div>

                            <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $event->title }}</h2>

                            <div class="flex flex-wrap text-gray-600 text-sm gap-2 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span>{{ date('d F Y', strtotime($event->start_event)) }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <span>{{ $event->location }}</span>
                                </div>
                            </div>

                            <a href="{{ route('events.show', $event->id) }}"
                               class="block text-center bg-[#7B0015] hover:bg-[#950019] text-white font-bold py-2 px-4 rounded-lg transition-all transform hover:scale-105">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <script>
        function openFullImage(src) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/90';
            modal.style.backdropFilter = 'blur(5px)';
            modal.innerHTML = `
                <div class="relative max-w-4xl max-h-[90vh] p-4">
                    <img src="${src}" alt="Full Image" class="max-w-full max-h-[80vh] object-contain">
                    <button type="button" class="absolute top-2 right-2 bg-white text-black p-2 rounded-full hover:bg-gray-200" id="close-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(modal);

            // Prevent scrolling when modal is open
            document.body.style.overflow = 'hidden';

            const closeButton = modal.querySelector('#close-modal');
            closeButton.addEventListener('click', function() {
                modal.remove();
                document.body.style.overflow = '';
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                    document.body.style.overflow = '';
                }
            });

            const escHandler = function(e) {
                if (e.key === 'Escape') {
                    modal.remove();
                    document.body.style.overflow = '';
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);
        }
    </script>
</x-guest-layout>