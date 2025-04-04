<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Ambil event yang akan datang untuk ditampilkan di "Event Recommendations"
        $events = Event::with('tickets')
                 ->where('start_event', '>', now())
                 ->take(3)
                 ->get();

        // Ambil kategori untuk ditampilkan di section "Category"
        $categories = Category::take(6)
                     ->get();

        // Ambil event populer untuk section "Top Events"
        $topEvents = Event::with('tickets')
                    ->withCount(['orders' => function($query) {
                        $query->whereHas('payment', function($q) {
                            $q->where('status', 'paid');
                        });
                    }])
                    ->orderBy('orders_count', 'desc')
                    ->take(3)
                    ->get();

        return view('welcome', compact('events', 'categories', 'topEvents'));
    }
}
