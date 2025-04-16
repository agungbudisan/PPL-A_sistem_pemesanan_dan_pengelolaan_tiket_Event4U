<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class SendETicket extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var Order
     */
    public $order;

    /**
     * Whether the user is a guest or not.
     *
     * @var bool
     */
    public $isGuest;

    /**
     * Create a new message instance.
     *
     * @param  Order  $order
     * @param  bool  $isGuest
     * @return void
     */
    public function __construct(Order $order, bool $isGuest)
    {
        // Make sure the order has all the required relationships
        $this->order = $order->load(['ticket.event', 'user']);
        $this->isGuest = $isGuest;

        Log::info('SendETicket created for order: ' . $order->id . ', isGuest: ' . ($isGuest ? 'Yes' : 'No'));
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address', 'example@example.com'),
            subject: 'E-Ticket Anda',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        $view = $this->isGuest ? 'emails.guest-eticket' : 'emails.eticket';

        Log::info('Using view template: ' . $view);

        return new Content(
            view: $view,
            with: [
                'order' => $this->order,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
