<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            // Null for the free plan, which is never bought through Stripe.
            $table->string('stripe_price_id')->nullable()->unique();
            $table->unsignedInteger('price_cents')->default(0);
            $table->char('currency', 3)->default('usd');
            $table->string('interval', 10)->default('month');
            $table->unsignedInteger('qr_limit');
            // Lifetime scans allowed per QR code.
            $table->unsignedInteger('scan_limit');
            $table->boolean('is_free')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
