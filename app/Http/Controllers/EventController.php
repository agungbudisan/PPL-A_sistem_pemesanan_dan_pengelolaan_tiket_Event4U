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
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy', 'adminShow']);
    }

    public function index(Request $request)
    {
        // Memulai query dasar untuk events
        $query = Event::with(['category', 'tickets.orders']);

        // Filter berdasarkan parameter pencarian
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('location', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan status (upcoming, ongoing, past)
        if ($request->filled('status')) {
            $now = now();
            if ($request->status === 'upcoming') {
                $query->where('start_event', '>', $now);
            } elseif ($request->status === 'ongoing') {
                $query->where('start_event', '<=', $now)
                      ->where('end_event', '>=', $now);
            } elseif ($request->status === 'past') {
                $query->where('end_event', '<', $now);
            }
        }

        // Dapatkan hasil query
        $events = $query->get();

        // Dapatkan semua kategori untuk filter dropdown
        $categories = Category::all();

        if (request()->is('admin*')) {
            return view('admin.events.index', compact('events', 'categories'));
        }
        
        return view('events.index', compact('events', 'categories'));
    }

    public function show(Event $event)
    {
        // Method ini untuk public user
        return view('events.show', compact('event'));
    }

    public function adminShow(Event $event)
    {
        // Method ini untuk admin
        return view('admin.events.show', compact('event'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
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
            'has_stage_layout' => 'nullable|boolean',
            'stage_layout' => 'nullable|required_if:has_stage_layout,1|image',
        ]);

        $path = $request->file('thumbnail')->store('thumbnails', 'public');

        $stageLayoutPath = null;
        if ($request->has('has_stage_layout') && $request->hasFile('stage_layout')) {
            $stageLayoutPath = $request->file('stage_layout')->store('stage_layouts', 'public');
        }

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'start_event' => $request->start_event,
            'end_event' => $request->end_event,
            'start_sale' => $request->start_sale,
            'end_sale' => $request->end_sale,
            'thumbnail' => $path,
            'stage_layout' => $stageLayoutPath,
            'category_id' => $request->category_id,
            'uid_admin' => Auth::id(),
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
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
            'has_stage_layout' => 'nullable|boolean',
            'stage_layout' => 'nullable|image',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'start_event' => $request->start_event,
            'end_event' => $request->end_event,
            'start_sale' => $request->start_sale,
            'end_sale' => $request->end_sale,
            'category_id' => $request->category_id,
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Handling stage layout
        if ($request->has('has_stage_layout')) {
            if ($request->hasFile('stage_layout')) {
                $data['stage_layout'] = $request->file('stage_layout')->store('stage_layouts', 'public');
            }
        } else {
            // Remove stage layout if checkbox is unchecked
            $data['stage_layout'] = null;
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
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

        return view('admin.analytics', compact('event', 'totalSales', 'totalTicketsSold'));
    }
}
