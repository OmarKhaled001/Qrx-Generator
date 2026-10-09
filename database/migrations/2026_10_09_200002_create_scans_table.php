<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No raw IP addresses: only a country code and a daily-keyed visitor hash.
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_code_id')->constrained()->cascadeOnDelete();
            $table->timestamp('scanned_at');
            $table->string('device', 10);
            $table->string('referrer_host')->nullable();
            $table->char('country', 2)->nullable();
            $table->char('visitor_hash', 64)->nullable();

            $table->index(['qr_code_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
