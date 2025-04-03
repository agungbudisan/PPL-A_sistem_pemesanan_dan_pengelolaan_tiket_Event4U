<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $totalEvents = Event::count();
        $upcomingEvents = Event::where('start_event', '>', now())->count();
        $categories = Category::count();
        $activeTickets = Ticket::sum('quota_avail');

        $recentEvents = Event::with('category')
            ->latest()
            ->take(5)
            ->get();

        $categoryData = Category::withCount('events')->get();
        $categoryNames = $categoryData->pluck('name')->toArray();
        $eventCounts = $categoryData->pluck('events_count')->toArray();

        return view('admin.dashboard', array_merge(compact(
            'totalEvents',
            'upcomingEvents',
            'categories',
            'activeTickets',
            'recentEvents',
            'categoryNames',
            'eventCounts'
        ), [
            'title' => 'Dashboard Admin',
            'header' => 'Dashboard Utama'
        ]));
    }

    public function analytics()
    {
        $eventId = request()->route('eventId');

        // Data dasar yang selalu dikirim
        $viewData = [
            'title' => 'Analitik Acara',
            'header' => 'Analitik Acara',
            'events' => Event::with(['tickets.orders', 'category'])->get()
        ];

        if ($eventId) {
            $event = Event::with(['tickets.orders', 'category'])->findOrFail($eventId);

            // Hitung total penjualan dan tiket terjual
            $totalSales = 0;
            $totalTicketsSold = 0;
            $ticketTypes = [];

            foreach ($event->tickets as $ticket) {
                $sold = $ticket->orders->sum('quantity');
                $revenue = $ticket->price * $sold;

                $totalSales += $revenue;
                $totalTicketsSold += $sold;

                $ticketTypes[] = [
                    'name' => $ticket->ticket_class,
                    'sold' => $sold,
                    'revenue' => $revenue,
                    'quota' => $ticket->quota_avail,
                    'percentage' => $ticket->quota_avail > 0 ? ($sold / $ticket->quota_avail) * 100 : 0
                ];
            }

            // Data untuk chart
            $chartData = [
                'labels' => collect($ticketTypes)->pluck('name'),
                'sold' => collect($ticketTypes)->pluck('sold'),
                'revenue' => collect($ticketTypes)->pluck('revenue')
            ];

            // Gabungkan dengan data tambahan
            $viewData = array_merge($viewData, [
                'event' => $event,
                'totalSales' => $totalSales,
                'totalTicketsSold' => $totalTicketsSold,
                'ticketTypes' => $ticketTypes,
                'chartData' => $chartData
            ]);
        }

        return view('admin.analytics', $viewData);
    }
}
