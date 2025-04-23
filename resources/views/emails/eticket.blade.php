<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #f0f0f0;
        }
        .ticket {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background-color: #f9f9f9;
        }
        .ticket-header {
            background-color: #4a86e8;
            color: white;
            padding: 10px;
            margin: -20px -20px 20px;
            border-radius: 8px 8px 0 0;
        }
        .details {
            margin: 20px 0;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .details table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            padding: 20px 0;
            border-top: 1px solid #f0f0f0;
        }
        .barcode {
            text-align: center;
            margin: 20px 0;
        }
        .qrcode {
            text-align: center;
            margin: 20px 0;
        }
        .qrcode img {
            width: 150px;
            height: 150px;
        }
        .qrcode-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .instructions {
            background-color: #fffde7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>E-Ticket Anda</h1>
    </div>

    <p>Halo {{ $order->name ?? ($order->user->name ?? 'Pelanggan') }},</p>

    <p>Terima kasih atas pembelian tiket Anda. Berikut adalah e-ticket untuk acara <strong>{{ $order->ticket->event->title }}</strong>.</p>

    <div class="ticket">
        <div class="ticket-header">
            <h2>{{ $order->ticket->event->title }}</h2>
        </div>

        <div class="details">
            <table>
                <tr>
                    <td>Nomor Referensi:</td>
                    <td>{{ $order->reference }}</td>
                </tr>
                <tr>
                    <td>Nama:</td>
                    <td>{{ $order->name ?? ($order->user->name ?? 'Pelanggan') }}</td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{ $order->email }}</td>
                </tr>
                <tr>
                    <td>Tanggal Acara:</td>
                    <td>
                        @if(isset($order->ticket->event->start_event))
                            {{ $order->ticket->event->start_event->format('d F Y') }}
                        @elseif(isset($order->ticket->event->event_date))
                            {{ $order->ticket->event->event_date->format('d F Y') }}
                        @else
                            Lihat detail acara
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Waktu Acara:</td>
                    <td>
                        @if(isset($order->ticket->event->start_event))
                            {{ $order->ticket->event->start_event->format('H:i') }} WIB
                        @elseif(isset($order->ticket->event->event_time))
                            {{ $order->ticket->event->event_time }}
                        @else
                            Lihat detail acara
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Lokasi:</td>
                    <td>{{ $order->ticket->event->location }}</td>
                </tr>
                <tr>
                    <td>Jenis Tiket:</td>
                    <td>{{ $order->ticket->ticket_class ?? $order->ticket->title }}</td>
                </tr>
                <tr>
                    <td>Jumlah Tiket:</td>
                    <td>{{ $order->quantity }}</td>
                </tr>
                <tr>
                    <td>Harga Tiket:</td>
                    <td>Rp{{ number_format($order->ticket->price, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Harga:</td>
                    <td>Rp{{ number_format($order->total_price, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="qrcode" style="text-align: center; margin: 20px 0;">
            <p>Referensi Tiket: <strong>{{ $order->reference }}</strong></p>

            @if(isset($hasQrCode) && $hasQrCode)
                <p>QR Code tiket telah dilampirkan ke email ini. Harap simpan dan tunjukkan saat masuk ke acara.</p>
            @else
                <p>Harap tunjukkan nomor referensi ini saat masuk ke acara.</p>
            @endif
        </div>
    </div>

    <div class="instructions">
        <h3>Petunjuk Penggunaan E-Ticket:</h3>
        <ol>
            <li>Simpan e-ticket ini dan tunjukkan saat registrasi di tempat acara.</li>
            <li>Anda dapat mencetak e-ticket ini atau menunjukkan versi digitalnya dari perangkat mobile Anda.</li>
            <li>Harap datang 30 menit sebelum acara dimulai untuk proses registrasi.</li>
            <li>Jika Anda memiliki pertanyaan, silakan hubungi kami di support@example.com</li>
        </ol>
    </div>

    <div class="footer">
        <p>Terima kasih telah membeli tiket melalui platform kami.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
