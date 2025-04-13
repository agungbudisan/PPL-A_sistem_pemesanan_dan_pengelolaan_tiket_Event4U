<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class AnalyticsExport implements FromArray
{
    protected $event;
    protected $totalSales;
    protected $totalTicketsSold;
    protected $ticketTypes;

    public function __construct($event, $totalSales, $totalTicketsSold, $ticketTypes)
    {
        $this->event = $event;
        $this->totalSales = $totalSales;
        $this->totalTicketsSold = $totalTicketsSold;
        $this->ticketTypes = $ticketTypes;
    }

    public function array(): array
    {
        // Mengembalikan array untuk diekspor ke Excel
        return [
            ['Event Title', $this->event->title],
            ['Total Sales', 'Rp' . number_format($this->totalSales, 0, ',', '.')],
            ['Total Tickets Sold', $this->totalTicketsSold],
            [],
            // Menambahkan data tiket
            ...array_map(function ($ticket) {
                return [
                    $ticket['name'],
                    $ticket['sold'],
                    $ticket['revenue'],
                    $ticket['quota'],
                    number_format($ticket['percentage'], 2, ',', '.') . '%'
                ];
            }, $this->ticketTypes),
        ];
    }
}

