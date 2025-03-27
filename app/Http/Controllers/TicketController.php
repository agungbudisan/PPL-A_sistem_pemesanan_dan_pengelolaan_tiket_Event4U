<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Event $event)
    {
        $tickets = $event->tickets;
        return view('tickets.index', compact('tickets', 'event'));
    }

    public function create(Event $event)
    {
        return view('tickets.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'ticket_class' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quota_avail' => 'required|integer',
        ]);

        $event->tickets()->create([
            'ticket_class' => $request->ticket_class,
            'description' => $request->description,
            'price' => $request->price,
            'quota_avail' => $request->quota_avail,
        ]);

        return redirect()->route('tickets.index', $event)->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        return view('tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'ticket_class' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quota_avail' => 'required|integer',
        ]);

        $ticket->update([
            'ticket_class' => $request->ticket_class,
            'description' => $request->description,
            'price' => $request->price,
            'quota_avail' => $request->quota_avail,
        ]);

        return redirect()->route('tickets.index', $ticket->event)->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index', $ticket->event)->with('success', 'Ticket deleted successfully.');
    }
}
