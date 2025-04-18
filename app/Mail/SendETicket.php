<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class SendETicket extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     */
    public $order;

    /**
     * Whether the user is a guest or not.
     */
    public $isGuest;

    /**
     * QR code as base64
     */
    public $qrCodeBase64;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, bool $isGuest)
    {
        // Make sure the order has all the required relationships
        $this->order = $order->load(['ticket.event', 'user']);
        $this->isGuest = $isGuest;

        // Generate QR code sebagai Base64
        $this->generateQrCodeBase64();

        Log::info('SendETicket created for order: ' . $order->id . ', isGuest: ' . ($isGuest ? 'Yes' : 'No'));
    }

    /**
     * Generate QR code menggunakan format SVG dan diconvert ke Base64
     */
    protected function generateQrCodeBase64()
    {
        try {
            // Data untuk QR code
            $qrData = [
                'reference' => $this->order->reference ?? $this->order->id,
                'event' => $this->order->ticket->event->title,
                'ticket_type' => $this->order->ticket->ticket_class ?? $this->order->ticket->title,
                'quantity' => $this->order->quantity,
                'name' => $this->isGuest ? ($this->order->guest_name ?? $this->order->name) : ($this->order->user->name ?? $this->order->name),
                'email' => $this->order->email
            ];

            // Create JSON data
            $jsonData = json_encode($qrData);

            // Generate QR code sebagai SVG (tidak memerlukan Imagick)
            $svgQrCode = QrCode::format('svg')
                ->size(200)
                ->margin(1)
                ->generate($jsonData);

            // Convert SVG ke Base64
            $this->qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgQrCode);

            Log::info('QR code generated as base64 SVG');
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            $this->qrCodeBase64 = null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address', 'example@example.com'),
            subject: 'E-Ticket Anda - ' . ($this->order->reference ?? $this->order->id),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = $this->isGuest ? 'emails.guest-eticket' : 'emails.eticket';

        Log::info('Using view template: ' . $view);

        return new Content(
            view: $view,
            with: [
                'order' => $this->order,
                'hasQrCode' => !empty($this->qrCodeBase64),
                'qrCodeBase64' => $this->qrCodeBase64
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        // Generate PDF dengan QR code base64
        if ($this->isGuest) {
            $pdf = Pdf::loadView('pdfs.guest-eticket', [
                'order' => $this->order,
                'qrCodeBase64' => $this->qrCodeBase64
            ]);

            $attachments[] = Attachment::fromData(fn () => $pdf->output(), 'e-ticket-' . ($this->order->reference ?? $this->order->id) . '.pdf')
                ->withMime('application/pdf');

            Log::info('Attaching guest PDF e-ticket');
        } else {
            $pdf = Pdf::loadView('pdfs.eticket', [
                'order' => $this->order,
                'qrCodeBase64' => $this->qrCodeBase64
            ]);

            $attachments[] = Attachment::fromData(fn () => $pdf->output(), 'e-ticket-' . $this->order->id . '.pdf')
                ->withMime('application/pdf');

            Log::info('Attaching user PDF e-ticket');
        }

        return $attachments;
    }
}
