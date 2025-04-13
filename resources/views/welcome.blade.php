<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event 4 U</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Main Content -->
    <main class="container mx-auto mt-4 px-4 lg:px-6">

        <!-- Hero Section - Enhanced with better layout and animation -->
        <section class="relative h-[400px] sm:h-[450px] md:h-[500px] bg-cover bg-center text-white rounded-xl overflow-hidden shadow-xl mb-12" style="background-image: url('/images/iklan.png');">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
            <div class="absolute inset-0 flex items-center">
                <div class="ml-8 md:ml-16 max-w-lg p-6 rounded-lg animate-fadeIn">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4 tracking-tight">Enjoy the Show</h1>
                    <p class="text-xl mb-6 text-gray-100">Discover your next favorite event. Grab your seat in just a few clicks.</p>
                    <a href="{{ route('events.index') }}" class="inline-block bg-[#7B0015] hover:bg-[#950019] text-white font-bold py-3 px-8 rounded-full transition-all transform hover:scale-105 shadow-lg">
                        Browse Events <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Event Recommendations - Added animations and improved card design -->
        <section class="mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold relative">
                    Event Recommendations
                    <span class="block h-1 w-24 bg-[#7B0015] mt-2"></span>
                </h2>
                <a href="{{ route('events.index') }}" class="text-[#7B0015] hover:text-[#950019] font-semibold flex items-center">
                    View All <i class="fas fa-chevron-right ml-2"></i>
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($events as $event)
                    <x-event-card :event="$event" />
                @empty
                    <div class="col-span-3 bg-white p-12 rounded-lg shadow text-center">
                        <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-xl">No upcoming events available.</p>
                        <p class="text-gray-400 mt-2">Check back soon for new exciting events!</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Top Events - Redesigned with hover effects -->
        <section class="mb-16 bg-gradient-to-r from-[#7B0015] to-[#AF0020] text-white p-8 rounded-xl shadow-lg">
            <h2 class="text-3xl font-bold mb-8 flex items-center">
                <i class="fas fa-crown mr-3 text-yellow-300"></i> Top Events
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($topEvents as $index => $event)
                    <div class="group relative overflow-hidden rounded-lg transition-transform duration-300 transform hover:scale-105">
                        <div class="absolute top-0 left-0 w-16 h-16 {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-300' : 'bg-[#CD7F32]') }} rounded-br-lg flex items-center justify-center shadow-lg z-10">
                            <span class="text-3xl font-bold {{ $index === 1 ? 'text-gray-800' : 'text-white' }}">{{ $index + 1 }}</span>
                        </div>
                        @if($event->thumbnail)
                            <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="w-full h-56 object-cover">
                        @else
                            <div class="w-full h-56 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="font-bold text-lg">{{ $event->title }}</h3>
                            <p class="text-sm text-gray-300">{{ date('d F Y', strtotime($event->start_event)) }}</p>
                            <a href="{{ route('events.show', $event->id) }}" class="mt-2 inline-block text-sm text-white hover:text-yellow-300">
                                View Details <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    @for ($i = 0; $i < 3; $i++)
                        <div class="group relative overflow-hidden rounded-lg transition-transform duration-300 transform hover:scale-105">
                            <div class="absolute top-0 left-0 w-16 h-16 {{ $i === 0 ? 'bg-yellow-500' : ($i === 1 ? 'bg-gray-300' : 'bg-[#CD7F32]') }} rounded-br-lg flex items-center justify-center shadow-lg">
                                <span class="text-3xl font-bold {{ $i === 1 ? 'text-gray-800' : 'text-white' }}">{{ $i + 1 }}</span>
                            </div>
                            <img src="/images/event_{{ $i + 1 }}.png" class="w-full h-56 object-cover" alt="Coming soon event">
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                <h3 class="font-bold text-lg">Event Name</h3>
                                <p class="text-sm text-gray-300">Coming soon</p>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </section>

        <!-- Category - Redesigned with icons and better styling -->
        <section class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold relative">
                    Categories
                    <span class="block h-1 w-24 bg-[#7B0015] mt-2"></span>
                </h2>

                <div class="flex items-center">
                    <button id="toggle-button" class="text-lg text-[#7B0015] hover:text-[#950019] font-bold flex items-center transition-transform duration-300 transform rotate-0">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <a id="show-more-link" href="{{ route('events.index') }}" class="ml-4 text-[#7B0015] hover:text-[#950019] font-semibold hidden">
                        All Categories
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
                @forelse($categories as $category)
                    <a href="{{ route('events.index', ['category_id' => $category->id]) }}" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all transform hover:scale-105">
                        <div class="h-36 bg-[#F3F4F6] flex items-center justify-center">
                            @php
                                // Logic untuk menentukan ikon berdasarkan nama kategori
                                $name = strtolower($category->name);
                                $icon = 'calendar-alt';

                                if (strpos($name, 'konser') !== false || strpos($name, 'musik') !== false) {
                                    $icon = 'music';
                                } elseif (strpos($name, 'seminar') !== false) {
                                    $icon = 'microphone';
                                } elseif (strpos($name, 'festival') !== false) {
                                    $icon = 'child';
                                } elseif (strpos($name, 'workshop') !== false) {
                                    $icon = 'tools';
                                } elseif (strpos($name, 'pameran') !== false) {
                                    $icon = 'image';
                                } elseif (strpos($name, 'kompetisi') !== false) {
                                    $icon = 'trophy';
                                } elseif (strpos($name, 'teater') !== false || strpos($name, 'pertunjukan') !== false) {
                                    $icon = 'theater-masks';
                                } elseif (strpos($name, 'olahraga') !== false || strpos($name, 'sport') !== false) {
                                    $icon = 'running';
                                }
                            @endphp

                            <!-- Tampilkan ikon atau gambar kategori -->
                            @if($category->icon && file_exists(public_path('storage/' . $category->icon)))
                                <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="h-24 w-24 object-contain">
                            @else
                                <!-- Fallback ke Font Awesome icon -->
                                <div class="h-24 w-24 rounded-full bg-[#7B0015] flex items-center justify-center text-white text-3xl">
                                    <i class="fas fa-{{ $icon }}"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 text-center">
                            <h3 class="font-bold">{{ $category->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $category->events_count ?? 0 }} Events</p>
                        </div>
                    </a>
                @empty
                    <!-- Tampilkan kategori placeholder jika tidak ada kategori -->
                    @foreach(['Festival', 'Konser', 'Pameran', 'Workshop', 'Seminar', 'Kompetisi'] as $index => $name)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all transform hover:scale-105">
                            <div class="h-36 bg-[#F3F4F6] flex items-center justify-center">
                                @php
                                    $placeholderIcons = ['child', 'music', 'image', 'tools', 'microphone', 'trophy'];
                                    $icon = $placeholderIcons[$index] ?? 'calendar-alt';
                                @endphp
                                <div class="h-24 w-24 rounded-full bg-[#7B0015] flex items-center justify-center text-white text-3xl">
                                    <i class="fas fa-{{ $icon }}"></i>
                                </div>
                            </div>
                            <div class="p-4 text-center">
                                <h3 class="font-bold">{{ $name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">0 Events</p>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </section>

        <!-- CTA Section -->
        <section class="mb-16 bg-gradient-to-r from-[#7B0015] to-[#AF0020] text-white p-8 rounded-xl shadow-lg">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-6 md:mb-0 md:mr-6">
                    <h2 class="text-3xl font-bold mb-4">Ready to Find Your Next Event?</h2>
                    <p class="text-lg opacity-90 max-w-lg">Discover amazing events happening around you. Purchase tickets easily and never miss out on exciting experiences!</p>
                </div>
                <div class="flex flex-col space-y-3">
                    <a href="{{ route('events.index') }}" class="bg-white text-[#7B0015] hover:bg-gray-100 font-bold py-3 px-8 rounded-full text-center transition-all transform hover:scale-105 shadow-lg">
                        Browse Events <i class="fas fa-search ml-2"></i>
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="bg-transparent border-2 border-white text-white hover:bg-white/10 font-bold py-3 px-8 rounded-full text-center transition-all">
                        Create Account
                    </a>
                    @endguest
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- Add required CSS for animations -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease-out;
        }
    </style>

    <script>
        // Toggle for category section
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggle-button');
            const showMoreLink = document.getElementById('show-more-link');

            if (toggleButton && showMoreLink) {
                toggleButton.addEventListener('click', function() {
                    // Toggle visibility of "All Categories" link
                    showMoreLink.classList.toggle('hidden');

                    // Rotate arrow icon
                    this.classList.toggle('rotate-90');
                });
            }
        });
    </script>
</body>
</html>
