<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update']);
    }

    public function index()
    {
        $events = Event::with('category')->get();
        return view('events.index', compact('events'));
    }

    public function showAnalytics($eventId)
    {
        $event = Event::with('tickets')->findOrFail($eventId);

        // Menghitung total penjualan berdasarkan tiket yang terjual
        $totalSales = 0;
        foreach ($event->tickets as $ticket) {
            $totalSales += $ticket->price * $ticket->orders->sum('quantity'); // Jumlahkan harga tiket dengan jumlah yang dipesan
        }

        // Menghitung total tiket yang terjual
        $totalTicketsSold = 0;
        foreach ($event->tickets as $ticket) {
            $totalTicketsSold += $ticket->orders->sum('quantity');
        }

        return view('events.analytics', compact('event', 'totalSales', 'totalTicketsSold'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_event' => 'required|date',
            'end_event' => 'required|date',
            'start_sale' => 'required|date',
            'end_sale' => 'required|date',
            'thumbnail' => 'required|image',
            'category_id' => 'required|exists:categories,id',
        ]);

        $path = $request->file('thumbnail')->store('thumbnails', 'public');

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'start_event' => $request->start_event,
            'end_event' => $request->end_event,
            'start_sale' => $request->start_sale,
            'end_sale' => $request->end_sale,
            'thumbnail' => $path,
            'category_id' => $request->category_id,
            'uid_admin' => Auth::id(),
        ]);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $categories = Category::all();
        return view('events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_event' => 'required|date',
            'end_event' => 'required|date',
            'start_sale' => 'required|date',
            'end_sale' => 'required|date',
            'thumbnail' => 'nullable|image',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $event->thumbnail = $path;
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'start_event' => $request->start_event,
            'end_event' => $request->end_event,
            'start_sale' => $request->start_sale,
            'end_sale' => $request->end_sale,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
