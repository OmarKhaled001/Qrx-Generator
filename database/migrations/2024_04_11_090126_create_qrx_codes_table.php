<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qrx_codes', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('type');
			$table->string('code')->unique();
			$table->string('path')->unique();
			$table->integer('scan_count')->default('0');
			$table->string('status')->default('active');
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qrx_codes');
    }
};
