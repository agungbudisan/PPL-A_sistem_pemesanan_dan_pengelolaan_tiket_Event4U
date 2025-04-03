<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event 4 U</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900" style="background-image: url('/images/main_bg.png'); background-repeat: repeat;">

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Main Content -->
    <main class="container mx-auto mt-8 px-6 bg-repeat">

        <!-- Hero Section -->
        <section class="relative h-[200px] bg-cover bg-center text-white flex items-center justify-end pr-8" style="background-image: url('/images/iklan.png');">
            <div class="text-right bg-black bg-opacity-50 p-8 rounded max-w-md">
                <h2 class="text-4xl font-bold">Enjoy the Show</h2>
                <p class="text-lg mt-2"> Discover your next favorite event. Grab your seat in just a few clicks.</p>
            </div>
        </section>

        <!-- Event Recommendations -->
        <section class="mt-8">
            <h3 class="text-3xl font-bold mb-4">Event Recommendations</h3>
            <div class="grid md:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <x-event-card :event="$event" />
                @empty
                    <p class="text-gray-500">No upcoming events available.</p>
                @endforelse
            </div>
        </section>


        <!-- Top Events -->
        <section class="mt-8 bg-[#7B0015] text-white p-6 rounded">
            <h3 class="text-1xl font-bold mb-4">Top Events !</h3>
            <div class="flex justify-between">
                <div class="flex items-center w-1/3">
                    <p class="text-8xl font-bold flex-[1] text-right pr-4">1</p>
                    <div class="flex-[2] aspect-[16/9]">
                        <img src="/images/event_1.png" class="w-full h-full object-cover rounded">
                    </div>
                </div>
                <div class="flex items-center w-1/3">
                    <p class="text-8xl font-bold flex-[1] text-right pr-4">2</p>
                    <div class="flex-[2] aspect-[16/9]">
                        <img src="/images/event_2.png" class="w-full h-full object-cover rounded">
                    </div>
                </div>
                <div class="flex items-center w-1/3">
                    <p class="text-8xl font-bold flex-[1] text-right pr-4">3</p>
                    <div class="flex-[2] aspect-[16/9]">
                        <img src="/images/event_3.png" class="w-full h-full object-cover rounded">
                    </div>
                </div>
            </div>
        </section>

        <!-- Category -->
        <section class="mt-8">
            <h3 class="text-3xl font-bold mb-4">Category</h3>
            <div class="grid grid-cols-4 md:grid-cols-4 gap-6">
                <x-category-card image="/images/festival.png" title="Festival" />
                <x-category-card image="/images/konser.png" title="Konser" />
                <x-category-card image="/images/pameran.png" title="Pameran" />

                <!-- Tombol Selengkapnya -->
                <a href="{{ route('categories.index') }}" class="flex flex-col items-center justify-center">
                    <div class="w-56 h-56 flex items-center justify-center bg-[rgba(156,160,171,0.60)] rounded-full relative">
                        <img src="/images/selengkapnya.png" class="w-40 h-40 object-contain">
                        <p class="absolute bottom-2 left-1/2 transform -translate-x-1/2 text-lg font-bold text-red-600">Selengkapnya...</p>
                    </div>
                </a>
            </div>
        </section>


        <!-- Contac Us -->
        <section class=" justify-center mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 max-w-6xl mx-auto p-4 gap-4">
                <div class="bg-white p-8 rounded shadow">
                    <h3 class="text-3xl font-bold">Contact Us</h3>

                    <div class="flex justify-between mt-12">
                        <div class="flex-1">
                            <h4 class="text-xs">OUR ADDRESS</h4>
                        </div>
                        <div class="flex-1">
                            <p class="mt-4">57125 Surakarta<br>PT Tiket.in Indonesia<br>Gedung Selatan lantai 8</p>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <div class="flex-1">
                            <h4 class="text-xs">OUR CONTACT</h4>
                        </div>
                        <div class="flex-1">
                            <p class="mt-4">pnonton@tiket.in<br>+62 123456789</p>
                        </div>
                    </div>
                </div>


                <!-- Feedback Section -->
                <div class="bg-white p-8 rounded shadow">
                    <h3 class="text-xs font-bold mb-12">FEEDBACK FORM</h3>
                    <form>
                        <input type="text" placeholder="Name" class="w-full py-2 px-3 border-b-2 border-gray-300 focus:outline-none focus:border-[#7B0015] mb-4 bg-transparent">
                        <input type="email" placeholder="Email" class="w-full py-2 px-3 border-b-2 border-gray-300 focus:outline-none focus:border-[#7B0015] mb-4 bg-transparent">
                        <input type="text" placeholder="Phone Number" class="w-full py-2 px-3 border-b-2 border-gray-300 focus:outline-none focus:border-[#7B0015] mb-4 bg-transparent">
                        <textarea placeholder="Message" class="w-full py-2 px-3 border-b-2 border-gray-300 focus:outline-none focus:border-[#7B0015] mb-4 bg-transparent"></textarea>
                        <div class="flex justify-end">
                            <button class="bg-[#7B0015] text-white py-2 px-12 rounded-full">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.footer')

</body>
</html>
