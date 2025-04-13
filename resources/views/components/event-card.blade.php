<a href="{{ route('events.show', $event->id) }}" class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition group block">
    <div class="relative">
        @if($event->thumbnail)
            <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}"
                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
        @else
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-gray-400 text-4xl"></i>
            </div>
        @endif

        <!-- Status Badge -->
        @php
            $now = now();
            $isUpcoming = $now < $event->start_event;
            $isOngoing = $now >= $event->start_event && $now <= $event->end_event;
        @endphp
        <div class="absolute top-4 right-4">
            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-md
                {{ $isUpcoming ? 'bg-blue-600 text-white' : ($isOngoing ? 'bg-yellow-300 text-white' : 'bg-gray-600 text-white') }}">
                {{ $isUpcoming ? 'Upcoming' : ($isOngoing ? 'Ongoing' : 'Past') }}
            </span>
        </div>

        <!-- Category Badge -->
        <div class="absolute top-4 left-4">
            <span class="inline-block bg-white/80 backdrop-blur-sm text-[#7B0015] text-xs px-2 py-1 rounded-md">
                {{ $event->category->name ?? 'Event' }}
            </span>
        </div>
    </div>

    <div class="p-5">
        <h3 class="font-bold text-xl mb-2 line-clamp-2 group-hover:text-[#7B0015] transition">{{ $event->title }}</h3>

        <div class="flex flex-wrap text-sm text-gray-500 gap-4 mb-3">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt mr-2 text-[#7B0015]"></i>
                <span>{{ date('d M Y', strtotime($event->start_event)) }}</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-map-marker-alt mr-2 text-[#7B0015]"></i>
                <span class="truncate">{{ $event->location }}</span>
            </div>
        </div>

        @php
            $minPrice = $event->tickets->min('price') ?? 0;
            $maxPrice = $event->tickets->max('price') ?? 0;
            $isSaleOpen = $now >= $event->start_sale && $now <= $event->end_sale;
            $isEndedSale = $now > $event->end_sale;

            // Format harga dengan format mata uang Rupiah yang benar
            $formattedMinPrice = 'Rp' . number_format($minPrice, 2, ',', '.');
            $formattedMaxPrice = 'Rp' . number_format($maxPrice, 2, ',', '.');
        @endphp

        <div class="flex justify-between items-center mt-4">
            <!-- Price range dengan format mata uang Rupiah yang benar -->
            <p class="font-bold text-[#7B0015]">
                @if($minPrice == $maxPrice)
                    {{ $formattedMinPrice }}
                @else
                    {{ $formattedMinPrice }} - {{ $formattedMaxPrice }}
                @endif
            </p>

            <!-- Ticket status indicator -->
            <span class="text-sm {{ $isSaleOpen ? 'text-green-600' : ($isEndedSale ? 'text-red-600' : 'text-gray-500') }}">
                <i class="fas {{ $isSaleOpen ? 'fa-ticket-alt' : ($isEndedSale ? 'fa-times-circle' : 'fa-clock') }} mr-1"></i>
                {{ $isSaleOpen ? 'Available' : ($now < $event->start_sale ? 'Soon' : 'Ended') }}
            </span>
        </div>
    </div>
</a>
