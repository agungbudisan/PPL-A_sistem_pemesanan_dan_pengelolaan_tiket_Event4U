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
                <div class="group relative overflow-hidden rounded-lg transition-transform duration-300 transform hover:scale-105">
                    <div class="absolute top-0 left-0 w-16 h-16 bg-yellow-500 rounded-br-lg flex items-center justify-center shadow-lg">
                        <span class="text-3xl font-bold">1</span>
                    </div>
                    <img src="/images/event_1.png" class="w-full h-56 object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                        <h3 class="font-bold text-lg">Event Name</h3>
                        <p class="text-sm text-gray-300">Coming soon</p>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-lg transition-transform duration-300 transform hover:scale-105">
                    <div class="absolute top-0 left-0 w-16 h-16 bg-gray-300 rounded-br-lg flex items-center justify-center shadow-lg">
                        <span class="text-3xl font-bold text-gray-800">2</span>
                    </div>
                    <img src="/images/event_2.png" class="w-full h-56 object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                        <h3 class="font-bold text-lg">Event Name</h3>
                        <p class="text-sm text-gray-300">Coming soon</p>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-lg transition-transform duration-300 transform hover:scale-105">
                    <div class="absolute top-0 left-0 w-16 h-16 bg-[#CD7F32] rounded-br-lg flex items-center justify-center shadow-lg">
                        <span class="text-3xl font-bold">3</span>
                    </div>
                    <img src="/images/event_3.png" class="w-full h-56 object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                        <h3 class="font-bold text-lg">Event Name</h3>
                        <p class="text-sm text-gray-300">Coming soon</p>
                    </div>
                </div>
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
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="h-36 bg-[#F3F4F6] flex items-center justify-center">
                        <img src="/images/festival.png" class="h-24 w-24 object-contain">
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="font-bold">Festival</h3>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="h-36 bg-[#F3F4F6] flex items-center justify-center">
                        <img src="/images/konser.png" class="h-24 w-24 object-contain">
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="font-bold">Konser</h3>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="h-36 bg-[#F3F4F6] flex items-center justify-center">
                        <img src="/images/pameran.png" class="h-24 w-24 object-contain">
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="font-bold">Pameran</h3>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="h-36 bg-[#F3F4F6] flex items-center justify-center">
                        <img src="/images/nobar.png" class="h-24 w-24 object-contain">
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="font-bold">Nonton Bareng</h3>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="h-36 bg-[#F3F4F6] flex items-center justify-center">
                        <img src="/images/pertunjukan.png" class="h-24 w-24 object-contain">
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="font-bold">Pertunjukan</h3>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="h-36 bg-[#F3F4F6] flex items-center justify-center">
                        <img src="/images/seminar.png" class="h-24 w-24 object-contain">
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="font-bold">Seminar</h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Us - Redesigned with better layout and effects -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold mb-8 relative">
                Get In Touch
                <span class="block h-1 w-24 bg-[#7B0015] mt-2"></span>
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-lg p-8 transform transition-all hover:shadow-xl">
                    <h3 class="text-2xl font-bold mb-6">Contact Us</h3>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-[#7B0015] rounded-full p-3 mr-4">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-xs uppercase text-gray-500 mb-2">OUR ADDRESS</h4>
                                <p class="text-gray-700">57125 Surakarta<br>PT Tiket.in Indonesia<br>Gedung Selatan lantai 8</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-[#7B0015] rounded-full p-3 mr-4">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-xs uppercase text-gray-500 mb-2">OUR CONTACT</h4>
                                <p class="text-gray-700">pnonton@tiket.in</p>
                                <p class="text-gray-700">+62 123456789</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-[#7B0015] rounded-full p-3 mr-4">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <h4 class="text-xs uppercase text-gray-500 mb-2">BUSINESS HOURS</h4>
                                <p class="text-gray-700">Monday - Friday: 9AM - 5PM</p>
                                <p class="text-gray-700">Saturday: 10AM - 2PM</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex space-x-4">
                        <a href="#" class="bg-gray-100 hover:bg-gray-200 rounded-full p-3 transition-colors">
                            <i class="fab fa-facebook-f text-[#7B0015]"></i>
                        </a>
                        <a href="#" class="bg-gray-100 hover:bg-gray-200 rounded-full p-3 transition-colors">
                            <i class="fab fa-instagram text-[#7B0015]"></i>
                        </a>
                        <a href="#" class="bg-gray-100 hover:bg-gray-200 rounded-full p-3 transition-colors">
                            <i class="fab fa-twitter text-[#7B0015]"></i>
                        </a>
                        <a href="#" class="bg-gray-100 hover:bg-gray-200 rounded-full p-3 transition-colors">
                            <i class="fab fa-youtube text-[#7B0015]"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8 transform transition-all hover:shadow-xl">
                    <h3 class="text-2xl font-bold mb-6">Send Us a Message</h3>

                    <form class="space-y-6">
                        <div class="relative">
                            <input type="text" placeholder="Your Name" class="w-full py-3 px-4 border-b-2 border-gray-300 focus:outline-none focus:border-[#7B0015] bg-transparent">
                            <i class="fas fa-user absolute right-3 top-3 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <input type="email" placeholder="Your Email" class="w-full py-3 px-4 border-b-2 border-gray-300 focus:outline-none focus:border-[#7B0015] bg-transparent">
                            <i class="fas fa-envelope absolute right-3 top-3 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <input type="text" placeholder="Phone Number" class="w-full py-3 px-4 border-b-2 border-gray-300 focus:outline-none focus:border-[#7B0015] bg-transparent">
                            <i class="fas fa-phone absolute right-3 top-3 text-gray-400"></i>
                        </div>

                        <div class="relative">
                            <textarea placeholder="Your Message" rows="4" class="w-full py-3 px-4 border-b-2 border-gray-300 focus:outline-none focus:border-[#7B0015] bg-transparent resize-none"></textarea>
                            <i class="fas fa-comment absolute right-3 top-3 text-gray-400"></i>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#7B0015] hover:bg-[#950019] text-white font-bold py-3 px-8 rounded-full transition-all transform hover:scale-105 shadow-lg flex items-center">
                                Send Message <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </div>
                    </form>
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
        const toggleButton = document.getElementById('toggle-button');
        const showMoreLink = document.getElementById('show-more-link');

        toggleButton.addEventListener('click', function() {
            // Toggle visibility of "All Categories" link
            showMoreLink.classList.toggle('hidden');

            // Rotate arrow icon
            this.classList.toggle('rotate-90');
        });
    </script>
</body>
</html>
