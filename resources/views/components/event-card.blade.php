<a href="{{ route('events.show', $event->id) }}" class="bg-[rgba(156,160,171,0.60)] p-4 rounded shadow block hover:shadow-lg transition duration-300">
    <div class="aspect-[16/9]">
        <img src="{{ asset('storage/' . $event->thumbnail) }}" class="w-full h-full object-cover rounded" alt="{{ $event->title }}">
    </div>
    <h4 class="mt-2 font-bold uppercase">{{ $event->title }}</h4>
    <p class="text-sm">{{ date('d F Y', strtotime($event->start_event)) }}</p>

    @php
        $minPrice = $event->tickets->min('price') ?? 0;
        $maxPrice = $event->tickets->max('price') ?? 0;
    @endphp

    <p class="font-bold text-red-600">
        Rp{{ number_format($minPrice, 0, ',', '.') }} - Rp{{ number_format($maxPrice, 0, ',', '.') }}
    </p>
</a>
