<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials and configurations for Midtrans.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'append_notif_url' => true,

    // Durasi kedaluwarsa per metode pembayaran (dalam menit)
    'expiry_durations' => [
        'default' => 60, // 1 jam default
        'credit_card' => 60, // 1 jam untuk kartu kredit
        'bank_transfer' => 1440, // 24 jam untuk transfer bank
        'echannel' => 1440, // 24 jam untuk Mandiri Bill
        'gopay' => 15, // 15 menit untuk Gopay
        'shopeepay' => 15, // 15 menit untuk ShopeePay
        'qris' => 15, // 15 menit untuk QRIS
        'cstore' => 1440, // 24 jam untuk convenience stores
        'akulaku' => 1440, // 24 jam untuk Akulaku
        'kredivo' => 1440, // 24 jam untuk Kredivo
    ],
];
