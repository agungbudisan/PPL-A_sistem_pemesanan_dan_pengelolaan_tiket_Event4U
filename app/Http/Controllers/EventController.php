<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;
use Illuminate\Validation\Rule;
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
            'thumbnail' => 'required|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'has_stage_layout' => 'sometimes|boolean',
            'stage_layout' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->boolean('has_stage_layout');
                }),
                'nullable',
                'image',
                'max:2048',
            ],
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
            'uid_admin' => Auth::id(),
            'has_stage_layout' => $request->boolean('has_stage_layout'),
        ];

        // Process thumbnail image
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $data['thumbnail'] = $request->file('thumbnail')->store('events/thumbnails', 'public');
        }

        // Process stage layout image if provided
        if ($request->boolean('has_stage_layout')) {
            if ($request->hasFile('stage_layout') && $request->file('stage_layout')->isValid()) {
                $data['stage_layout'] = $request->file('stage_layout')->store('events/layouts', 'public');
            } else {
                return back()->withInput()->withErrors([
                    'stage_layout' => 'Layout panggung wajib diupload jika opsi ini dipilih'
                ]);
            }
        } else {
            $data['stage_layout'] = null;
        }

        try {
            // Create event with the data
            Event::create($data);

            return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating event: ' . $e->getMessage());
        }
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
            'thumbnail' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'has_stage_layout' => 'sometimes|boolean',
            'stage_layout' => [
                Rule::requiredIf(function () use ($request, $event) {
                    return $request->boolean('has_stage_layout') && !$event->stage_layout;
                }),
                'nullable',
                'image',
                'max:2048',
            ],
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
            'has_stage_layout' => $request->boolean('has_stage_layout'),
        ];

        // Update thumbnail if a new one is provided
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            // Hapus thumbnail lama jika ada
            if ($event->thumbnail && Storage::disk('public')->exists($event->thumbnail)) {
                Storage::disk('public')->delete($event->thumbnail);
            }

            $data['thumbnail'] = $request->file('thumbnail')->store('events/thumbnails', 'public');
        }

        // Update stage layout
        if ($request->boolean('has_stage_layout')) {
            if ($request->hasFile('stage_layout') && $request->file('stage_layout')->isValid()) {
                // Delete old stage layout if exists
                if ($event->stage_layout && Storage::disk('public')->exists($event->stage_layout)) {
                    Storage::disk('public')->delete($event->stage_layout);
                }
                $data['stage_layout'] = $request->file('stage_layout')->store('events/layouts', 'public');
            }
        } else {
            // If has_stage_layout is false, set stage_layout to null and delete file if exists
            if ($event->stage_layout && Storage::disk('public')->exists($event->stage_layout)) {
                Storage::disk('public')->delete($event->stage_layout);
            }
            $data['stage_layout'] = null;
        }

        try {
            $event->update($data);
            return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating event: ' . $e->getMessage());
        }
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
