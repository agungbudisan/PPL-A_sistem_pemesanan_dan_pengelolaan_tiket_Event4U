<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;

class Event4USeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin user
        $admin = User::create([
            'name' => 'Admin Event4U',
            'email' => 'admin@event4u.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Regular user
        $user = User::create([
            'name' => 'User Event4U',
            'email' => 'user@event4u.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Categories
        $categories = [
            [
                'name' => 'Konser Musik',
                'icon' => 'music',
                'description' => 'Konser musik dengan penampilan artis-artis ternama.'
            ],
            [
                'name' => 'Seminar',
                'icon' => 'graduation-cap',
                'description' => 'Seminar edukasi dengan tema-tema menarik.'
            ],
            [
                'name' => 'Festival',
                'icon' => 'campground',
                'description' => 'Festival dengan berbagai kegiatan menarik.'
            ],
            [
                'name' => 'Workshop',
                'icon' => 'tools',
                'description' => 'Workshop pengembangan keterampilan dan keahlian.'
            ],
            [
                'name' => 'Pameran',
                'icon' => 'paint-brush',
                'description' => 'Pameran seni, teknologi, dan inovasi.'
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create Events
        $now = Carbon::now();
        $events = [
            [
                'title' => 'Konser Musik Tahunan 2023',
                'description' => 'Konser musik tahunan dengan menampilkan berbagai artis terkenal. Akan ada penampilan spesial dari bintang tamu internasional.',
                'location' => 'Gelora Bung Karno, Jakarta',
                'start_event' => $now->copy()->addDays(30),
                'end_event' => $now->copy()->addDays(30)->addHours(5),
                'start_sale' => $now->copy()->subDays(30),
                'end_sale' => $now->copy()->addDays(25),
                'thumbnail' => 'thumbnails/concert.jpg',
                'stage_layout' => 'stage_layouts/concert_layout.jpg',
                'category_id' => 1,
                'uid_admin' => $admin->id,
            ],
            [
                'title' => 'Seminar Teknologi Masa Depan',
                'description' => 'Seminar teknologi yang membahas perkembangan terbaru dan masa depan teknologi. Pembicara dari perusahaan teknologi terkemuka akan hadir.',
                'location' => 'Jakarta Convention Center',
                'start_event' => $now->copy()->addDays(15),
                'end_event' => $now->copy()->addDays(15)->addHours(8),
                'start_sale' => $now->copy()->subDays(15),
                'end_sale' => $now->copy()->addDays(10),
                'thumbnail' => 'thumbnails/seminar.jpg',
                'category_id' => 2,
                'uid_admin' => $admin->id,
            ],
            [
                'title' => 'Festival Budaya Nusantara',
                'description' => 'Festival yang menampilkan kekayaan budaya nusantara dengan berbagai pertunjukan seni dan kuliner tradisional.',
                'location' => 'Taman Mini Indonesia Indah',
                'start_event' => $now->copy()->addDays(5),
                'end_event' => $now->copy()->addDays(7),
                'start_sale' => $now->copy()->subDays(30),
                'end_sale' => $now->copy()->addDays(4),
                'thumbnail' => 'thumbnails/festival.jpg',
                'category_id' => 3,
                'uid_admin' => $admin->id,
            ],
            [
                'title' => 'Workshop Digital Marketing',
                'description' => 'Workshop intensif tentang strategi digital marketing terkini untuk bisnis Anda.',
                'location' => 'Hotel Grand Mercure, Bandung',
                'start_event' => $now->copy()->subDays(5),
                'end_event' => $now->copy()->subDays(4),
                'start_sale' => $now->copy()->subDays(30),
                'end_sale' => $now->copy()->subDays(6),
                'thumbnail' => 'thumbnails/workshop.jpg',
                'category_id' => 4,
                'uid_admin' => $admin->id,
            ],
            [
                'title' => 'Pameran Seni Rupa Kontemporer',
                'description' => 'Pameran karya seni rupa kontemporer dari seniman-seniman berbakat Indonesia.',
                'location' => 'Museum Nasional, Jakarta',
                'start_event' => $now->copy()->addDays(45),
                'end_event' => $now->copy()->addDays(60),
                'start_sale' => $now->copy()->addDays(10),
                'end_sale' => $now->copy()->addDays(44),
                'thumbnail' => 'thumbnails/art_exhibition.jpg',
                'category_id' => 5,
                'uid_admin' => $admin->id,
            ],
        ];

        // Menyimpan gambar dummy
        $this->createDummyImages();

        foreach ($events as $eventData) {
            $event = Event::create($eventData);

            // Create Tickets for Events
            $this->createTickets($event);

            // Create Orders for Tickets
            $this->createOrders($event, $user);
        }
    }

    /**
     * Create tickets for an event
     *
     * @param Event $event
     * @return void
     */
    private function createTickets(Event $event)
    {
        $ticketTypes = [
            [
                'ticket_class' => 'VIP',
                'description' => 'Akses VIP dengan kursi terbaik dan souvenir eksklusif.',
                'price' => rand(500000, 2000000),
                'quota_avail' => rand(50, 100),
            ],
            [
                'ticket_class' => 'Gold',
                'description' => 'Akses Gold dengan kursi yang nyaman dan goodie bag.',
                'price' => rand(250000, 700000),
                'quota_avail' => rand(100, 300),
            ],
            [
                'ticket_class' => 'Silver',
                'description' => 'Akses Silver dengan kursi standar.',
                'price' => rand(100000, 300000),
                'quota_avail' => rand(300, 500),
            ],
            [
                'ticket_class' => 'Bronze',
                'description' => 'Akses Bronze dengan tempat berdiri.',
                'price' => rand(50000, 150000),
                'quota_avail' => rand(500, 1000),
            ],
        ];

        foreach ($ticketTypes as $ticketData) {
            $ticket = new Ticket($ticketData);
            $event->tickets()->save($ticket);
        }
    }

    /**
     * Create orders for tickets
     *
     * @param Event $event
     * @param User $user
     * @return void
     */
    private function createOrders(Event $event, User $user)
    {
        $tickets = $event->tickets;

        // Generate random number of orders for each ticket type
        foreach ($tickets as $ticket) {
            $maxOrders = min($ticket->quota_avail, rand(20, 50)); // Random number of orders, max = quota

            for ($i = 0; $i < $maxOrders; $i++) {
                $quantity = rand(1, 3); // Random quantity between 1-3 tickets per order

                // Calculate total price
                $totalPrice = $ticket->price * $quantity;

                // Create order
                $order = Order::create([
                    'total_price' => $totalPrice,
                    'quantity' => $quantity,
                    'email' => 'customer' . rand(1, 1000) . '@example.com',
                    'order_date' => now()->subDays(rand(1, 30))->subHours(rand(1, 24)),
                    'ticket_id' => $ticket->id,
                    // 'user_id' kolom dihapus karena tidak ada di tabel orders
                ]);

                // Create payment for the order
                $this->createPayment($order);
            }
        }
    }

    /**
     * Create payment for an order
     *
     * @param Order $order
     * @return void
     */
    private function createPayment(Order $order)
    {
        $paymentMethods = ['Credit Card', 'Bank Transfer', 'E-Wallet', 'Virtual Account'];
        $paymentStatuses = ['paid', 'pending', 'cancelled'];

        // Weighted statuses (80% paid, 15% pending, 5% cancelled)
        $statusRand = rand(1, 100);
        $status = $statusRand <= 80 ? 'paid' : ($statusRand <= 95 ? 'pending' : 'cancelled');

        // Set payment date for all statuses to avoid NULL
        $paymentDate = $status == 'paid'
            ? $order->order_date->addMinutes(rand(5, 60))
            : $order->order_date; // Menggunakan order_date untuk status selain 'paid'

        Payment::create([
            'method' => $paymentMethods[array_rand($paymentMethods)],
            'status' => $status,
            'payment_date' => $paymentDate,
            'order_id' => $order->id,
        ]);
    }

    /**
     * Create dummy image files
     *
     * @return void
     */
    private function createDummyImages()
    {
        // Make sure the storage directories exist
        if (!file_exists(public_path('storage/thumbnails'))) {
            mkdir(public_path('storage/thumbnails'), 0755, true);
        }

        if (!file_exists(public_path('storage/stage_layouts'))) {
            mkdir(public_path('storage/stage_layouts'), 0755, true);
        }

        // Create dummy thumbnails
        $this->createDummyImage(public_path('storage/thumbnails/concert.jpg'), 'Konser', '#3498db');
        $this->createDummyImage(public_path('storage/thumbnails/seminar.jpg'), 'Seminar', '#2ecc71');
        $this->createDummyImage(public_path('storage/thumbnails/festival.jpg'), 'Festival', '#e74c3c');
        $this->createDummyImage(public_path('storage/thumbnails/workshop.jpg'), 'Workshop', '#f39c12');
        $this->createDummyImage(public_path('storage/thumbnails/art_exhibition.jpg'), 'Pameran', '#9b59b6');

        // Create dummy stage layout
        $this->createDummyImage(public_path('storage/stage_layouts/concert_layout.jpg'), 'STAGE LAYOUT', '#34495e', 800, 600);
    }

    /**
     * Create a dummy image with text
     *
     * @param string $path
     * @param string $text
     * @param string $bgColor
     * @param int $width
     * @param int $height
     * @return void
     */
    private function createDummyImage($path, $text, $bgColor, $width = 400, $height = 300)
    {
        // Skip if GD extension is not available
        if (!extension_loaded('gd')) {
            return;
        }

        // Create image
        $image = imagecreatetruecolor($width, $height);

        // Allocate colors
        $bgColorRGB = $this->hex2rgb($bgColor);
        $background = imagecolorallocate($image, $bgColorRGB[0], $bgColorRGB[1], $bgColorRGB[2]);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        // Fill background
        imagefill($image, 0, 0, $background);

        // Add text
        $fontSize = 5; // Font size for imagestring (1-5)
        $text = strtoupper($text);

        // Calculate text position for center alignment
        // Use approximate width calculation instead of TTF
        $textWidth = strlen($text) * imagefontwidth($fontSize);
        $textHeight = imagefontheight($fontSize);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;

        // Add text using built-in font
        imagestring($image, $fontSize, $x, $y, $text, $textColor);

        // Save image
        imagejpeg($image, $path);
        imagedestroy($image);
    }

    /**
     * Convert hex color to RGB
     *
     * @param string $hex
     * @return array
     */
    private function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return [$r, $g, $b];
    }
}
