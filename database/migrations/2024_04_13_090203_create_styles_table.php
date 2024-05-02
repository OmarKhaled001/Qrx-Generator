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
        Schema::create('styles', function (Blueprint $table) {
            $table->id();
            $table->integer('size')->default('200');
			$table->string('style')->default('square');
			$table->string('color')->default('(0, 0, 0)');
			$table->string('bg_color')->default('(255, 255, 255)');
			$table->string('eye')->nullable();
			$table->string('margin')->nullable();
			$table->string('gradient_from')->nullable();
			$table->string('gradient_to')->nullable();
            $table->foreignId('qrx_code_id')->nullable()->references('id')->on('qrx_codes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('styles');
    }
};
