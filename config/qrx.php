<?php

return [

    /*
    | Limits of the free plan when no "free" row exists in the plans table
    | (the seeder creates one with these same values).
    */
    'free_plan' => [
        'qr_limit' => 3,
        'scan_limit' => 100,
    ],

    /*
    | Stripe price IDs of the paid plans (used by the plan seeder). Replace the
    | placeholders with real prices from your Stripe dashboard.
    */
    'stripe_prices' => [
        'starter' => env('STRIPE_PRICE_STARTER', 'price_starter_placeholder'),
        'business' => env('STRIPE_PRICE_BUSINESS', 'price_business_placeholder'),
        'agency' => env('STRIPE_PRICE_AGENCY', 'price_agency_placeholder'),
    ],

    'scans' => [
        // Header carrying the visitor's country, set by a trusted proxy/CDN.
        'country_header' => env('QRX_COUNTRY_HEADER', 'CF-IPCountry'),
    ],

    'demo' => [
        // Public landing-page QR demo: Livewire requests per minute per IP.
        'requests_per_minute' => 120,
    ],

    'locales' => [
        'en' => ['name' => 'English', 'dir' => 'ltr'],
        'ar' => ['name' => 'العربية', 'dir' => 'rtl'],
    ],

];
