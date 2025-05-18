<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Category;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Event4USeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan direktori storage publik sudah ada dan bisa diakses
        if (!Storage::disk('public')->exists('events/thumbnails')) {
            Storage::disk('public')->makeDirectory('events/thumbnails');
        }

        if (!Storage::disk('public')->exists('events/layouts')) {
            Storage::disk('public')->makeDirectory('events/layouts');
        }

        if (!Storage::disk('public')->exists('categories/icons')) {
            Storage::disk('public')->makeDirectory('categories/icons');
        }

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
                'description' => 'Konser musik dengan penampilan artis-artis ternama.'
            ],
            [
                'name' => 'Seminar',
                'description' => 'Seminar edukasi dengan tema-tema menarik.'
            ],
            [
                'name' => 'Festival',
                'description' => 'Festival dengan berbagai kegiatan menarik.'
            ],
            [
                'name' => 'Workshop',
                'description' => 'Workshop pengembangan keterampilan dan keahlian.'
            ],
            [
                'name' => 'Pameran',
                'description' => 'Pameran seni, teknologi, dan inovasi.'
            ],
        ];

        $iconColors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'];

        foreach ($categories as $index => $categoryData) {
            // Generate icon image file
            $iconPath = $this->generateImageFile(
                strtoupper($categoryData['name']),
                $iconColors[$index] ?? '#3498db',
                200,
                200,
                'categories/icons/category_' . Str::slug($categoryData['name']) . '.png'
            );

            Category::create([
                'name' => $categoryData['name'],
                'icon' => $iconPath,
                'description' => $categoryData['description']
            ]);
        }

        // Create Events
        $now = Carbon::now();
        $events = [
            [
                'title' => 'Konser Musik Tahunan 2025',
                'description' => 'Konser musik tahunan dengan menampilkan berbagai artis terkenal. Akan ada penampilan spesial dari bintang tamu internasional.',
                'location' => 'Gelora Bung Karno, Jakarta',
                'start_event' => $now->copy()->addDays(30),
                'end_event' => $now->copy()->addDays(30)->addHours(5),
                'start_sale' => $now->copy()->subDays(30),
                'end_sale' => $now->copy()->addDays(25),
                'color' => '#3498db',
                'name' => 'Konser',
                'category_id' => 1,
                'has_stage_layout' => true
            ],
            [
                'title' => 'Seminar Teknologi Masa Depan',
                'description' => 'Seminar teknologi yang membahas perkembangan terbaru dan masa depan teknologi. Pembicara dari perusahaan teknologi terkemuka akan hadir.',
                'location' => 'Jakarta Convention Center',
                'start_event' => $now->copy()->addDays(15),
                'end_event' => $now->copy()->addDays(15)->addHours(8),
                'start_sale' => $now->copy()->subDays(15),
                'end_sale' => $now->copy()->addDays(10),
                'color' => '#2ecc71',
                'name' => 'Seminar',
                'category_id' => 2,
                'has_stage_layout' => false
            ],
            [
                'title' => 'Festival Budaya Nusantara',
                'description' => 'Festival yang menampilkan kekayaan budaya nusantara dengan berbagai pertunjukan seni dan kuliner tradisional.',
                'location' => 'Taman Mini Indonesia Indah',
                'start_event' => $now->copy()->addDays(5),
                'end_event' => $now->copy()->addDays(7),
                'start_sale' => $now->copy()->subDays(30),
                'end_sale' => $now->copy()->addDays(4),
                'color' => '#e74c3c',
                'name' => 'Festival',
                'category_id' => 3,
                'has_stage_layout' => false
            ],
            [
                'title' => 'Workshop Digital Marketing',
                'description' => 'Workshop intensif tentang strategi digital marketing terkini untuk bisnis Anda.',
                'location' => 'Hotel Grand Mercure, Bandung',
                'start_event' => $now->copy()->subDays(5),
                'end_event' => $now->copy()->subDays(4),
                'start_sale' => $now->copy()->subDays(30),
                'end_sale' => $now->copy()->subDays(6),
                'color' => '#f39c12',
                'name' => 'Workshop',
                'category_id' => 4,
                'has_stage_layout' => false
            ],
            [
                'title' => 'Pameran Seni Rupa Kontemporer',
                'description' => 'Pameran karya seni rupa kontemporer dari seniman-seniman berbakat Indonesia.',
                'location' => 'Museum Nasional, Jakarta',
                'start_event' => $now->copy()->addDays(45),
                'end_event' => $now->copy()->addDays(60),
                'start_sale' => $now->copy()->addDays(10),
                'end_sale' => $now->copy()->addDays(44),
                'color' => '#9b59b6',
                'name' => 'Pameran',
                'category_id' => 5,
                'has_stage_layout' => false
            ],
        ];

        foreach ($events as $index => $eventData) {
            // Generate thumbnail file path
            $slug = Str::slug($eventData['title']);
            $thumbnailPath = $this->generateImageFile(
                $eventData['name'],
                $eventData['color'],
                800,
                600,
                'events/thumbnails/' . $slug . '_' . time() . '.png'
            );

            $stageLayoutPath = null;
            if ($eventData['has_stage_layout']) {
                $stageLayoutPath = $this->generateImageFile(
                    'STAGE LAYOUT ' . $eventData['name'],
                    '#34495e',
                    1200,
                    800,
                    'events/layouts/' . $slug . '_layout_' . time() . '.png'
                );
            }

            // Remove temp data
            $color = $eventData['color'];
            $name = $eventData['name'];
            $has_stage_layout = $eventData['has_stage_layout'];

            unset($eventData['color']);
            unset($eventData['name']);
            unset($eventData['has_stage_layout']);

            // Set actual path values
            $eventData['thumbnail'] = $thumbnailPath;
            $eventData['stage_layout'] = $stageLayoutPath;
            $eventData['has_stage_layout'] = $has_stage_layout;

            // Add admin id
            $eventData['uid_admin'] = $admin->id;

            $event = Event::create($eventData);

            // Create Tickets for Events
            $this->createTickets($event);

            // Create Orders for Tickets
            $this->createOrders($event, $user);

            $this->command->info("Created event: {$eventData['title']}");
        }

        $this->command->info('Seeding completed successfully!');
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
            $maxOrders = min($ticket->quota_avail, rand(10, 25)); // Random number of orders, max = quota

            for ($i = 0; $i < $maxOrders; $i++) {
                $quantity = rand(1, 3); // Random quantity between 1-3 tickets per order
                $isUserOrder = rand(0, 10) > 8; // 20% chance for user order vs guest order
                $orderDate = now()->subDays(rand(1, 30))->subHours(rand(1, 24));

                // Set expiration time - for completed orders in the past, for pending orders in the future
                $expiresAt = rand(0, 1)
                    ? $orderDate->copy()->addHour() // Default 1 hour expiry
                    : $orderDate->copy()->addHours(rand(1, 24)); // Random expiry time

                // Generate reference for tracking
                $reference = 'TIX' . strtoupper(substr(md5(uniqid()), 0, 8));

                // Calculate total price
                $totalPrice = $ticket->price * $quantity;

                // Create order
                $orderData = [
                    'reference' => $reference,
                    'total_price' => $totalPrice,
                    'quantity' => $quantity,
                    'email' => $isUserOrder ? $user->email : 'customer' . rand(1, 1000) . '@example.com',
                    'order_date' => $orderDate,
                    'expires_at' => $expiresAt, // Add expires_at field
                    'ticket_id' => $ticket->id,
                ];

                // Add user data if user order
                if ($isUserOrder) {
                    $orderData['user_id'] = $user->id;
                } else {
                    // Add guest data if guest order
                    $orderData['guest_name'] = 'Guest Customer ' . rand(1, 100);
                    $orderData['guest_phone'] = '08' . rand(1111111111, 9999999999);
                }

                $order = Order::create($orderData);

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
        // Midtrans-compatible payment methods
        $paymentMethods = ['midtrans'];
        $paymentMethodDetails = [
            'credit_card', 'bank_transfer', 'gopay', 'shopeepay',
            'qris', 'cstore', 'akulaku', 'kredivo'
        ];

        $paymentStatuses = ['completed', 'pending', 'expired', 'failed'];

        // Weighted statuses (75% completed, 15% pending, 5% expired, 5% failed)
        $statusRand = rand(1, 100);
        $status = $statusRand <= 75
            ? 'completed'
            : ($statusRand <= 90
            ? 'pending'
            : ($statusRand <= 95
                ? 'expired'
                : 'failed'));

        // Set payment date for all statuses to avoid NULL
        $paymentDate = $status == 'completed'
            ? $order->order_date->addMinutes(rand(5, 60))
            : $order->order_date;

        // Create Midtrans-like transaction ID
        $transactionId = 'ORDER-' . $order->id . '-' . Str::random(8);

        // Create random snap token for simulating Midtrans integration
        $snapToken = Str::random(32);

        // Get expiry time based on payment method detail
        $paymentMethodDetail = $paymentMethodDetails[array_rand($paymentMethodDetails)];
        $expiryDuration = $this->getExpiryDurationForPaymentType($paymentMethodDetail);
        $expiresAt = now()->addMinutes($expiryDuration);

        // For completed payments, expires_at should be in the past
        if ($status === 'completed') {
            $expiresAt = $paymentDate->copy()->subMinutes(rand(5, 30));
        }
        // For failed/expired payments, expires_at should be in the past
        elseif (in_array($status, ['expired', 'failed'])) {
            $expiresAt = $paymentDate->copy()->subMinutes(rand(1, 10));
        }

        $paymentData = [
            'method' => $paymentMethods[array_rand($paymentMethods)],
            'payment_method_detail' => $paymentMethodDetail,
            'status' => $status,
            'transaction_id' => $transactionId,
            'snap_token' => $snapToken,
            'payment_date' => $paymentDate,
            'expires_at' => $expiresAt,
            'order_id' => $order->id,
        ];

        // Add sample payment instructions for some payment methods
        if (in_array($paymentMethodDetail, ['bank_transfer', 'cstore']) && in_array($status, ['pending', 'completed'])) {
            $instructions = $this->generatePaymentInstructions($paymentMethodDetail);
            if (!empty($instructions)) {
                $paymentData['payment_instruction'] = json_encode($instructions);
            }
        }

        // Add guest email for some orders
        if (isset($order->guest_name)) {
            $paymentData['guest_email'] = $order->email; // Use same email from order
        }

        Payment::create($paymentData);
    }

    /**
     * Get expiry duration based on payment type
     *
     * @param string $paymentType
     * @return int Minutes until expiry
     */
    private function getExpiryDurationForPaymentType($paymentType)
    {
        $durations = [
            'credit_card' => 60, // 1 hour
            'bank_transfer' => 1440, // 24 hours
            'echannel' => 1440, // 24 hours
            'gopay' => 15, // 15 minutes
            'shopeepay' => 15, // 15 minutes
            'qris' => 15, // 15 minutes
            'cstore' => 1440, // 24 hours
            'akulaku' => 1440, // 24 hours
            'kredivo' => 1440, // 24 hours
            'default' => 60, // 1 hour default
        ];

        return $durations[$paymentType] ?? $durations['default'];
    }

    /**
     * Generate fake payment instructions for testing
     *
     * @param string $paymentType
     * @return array|null
     */
    private function generatePaymentInstructions($paymentType)
    {
        if ($paymentType === 'bank_transfer') {
            $banks = ['bca', 'bni', 'bri', 'mandiri', 'permata'];
            $bank = $banks[array_rand($banks)];

            return [
                [
                    'bank' => strtoupper($bank),
                    'va_number' => rand(100000000000, 999999999999),
                    'instruction' => "Transfer to virtual account number before the payment expires"
                ]
            ];
        }
        elseif ($paymentType === 'cstore') {
            $stores = ['indomaret', 'alfamart'];
            $store = $stores[array_rand($stores)];

            return [
                [
                    'store' => ucfirst($store),
                    'payment_code' => rand(10000000, 99999999),
                    'instruction' => "Pay at any $store outlet with the payment code"
                ]
            ];
        }

        return null;
    }

    /**
     * Generate an image file and save it to storage
     *
     * @param string $text Text to display on the image
     * @param string $bgColor Background color in hex format
     * @param int $width Image width
     * @param int $height Image height
     * @param string $path Path to save the image to (relative to storage/public)
     * @return string Path to the saved image (relative to storage/public)
     */
    private function generateImageFile($text, $bgColor = '#3498db', $width = 400, $height = 300, $path = null)
    {
        // Skip if GD extension is not available
        if (!extension_loaded('gd')) {
            $this->command->warn('GD extension is not available. Cannot generate images.');
            return null;
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
        $textWidth = strlen($text) * imagefontwidth($fontSize);
        $textHeight = imagefontheight($fontSize);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;

        // Add text using built-in font
        imagestring($image, $fontSize, $x, $y, $text, $textColor);

        // Add more styling - border
        $borderColor = imagecolorallocate($image, 255, 255, 255);
        imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);

        // Add some gradient effect if width is large enough
        if ($width >= 400) {
            $overlayColor = imagecolorallocatealpha($image, 255, 255, 255, 110);
            imagefilledrectangle($image, 0, 0, $width, $height/4, $overlayColor);
        }

        // Generate unique path if not provided
        if (!$path) {
            $path = 'temp/' . Str::random(10) . '.png';
        }

        // Get the stream resource
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();

        // Save to storage
        Storage::disk('public')->put($path, $imageData);

        // Free up memory
        imagedestroy($image);

        return $path;
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
