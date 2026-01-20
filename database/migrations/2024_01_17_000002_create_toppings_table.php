<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Table: toppings
     * Purpose: Menyimpan data topping yang dapat ditambahkan ke produk
     */
    public function up(): void
    {
        Schema::create('toppings', function (Blueprint $table) {
            // Primary Key
            $table->id(); // bigIncrements (BIGINT UNSIGNED AUTO_INCREMENT)

            // Topping Information
            $table->string('name', 50); // Nama topping (max 50 karakter)
            $table->decimal('price', 10, 2); // Harga tambahan topping
            $table->enum('category', ['makanan', 'minuman']); // Kategori topping

            // Status
            $table->boolean('is_available')->default(true); // Ketersediaan topping

            // Timestamps (created_at, updated_at)
            $table->timestamps();

            // Indexes untuk performance
            $table->index('category'); // Index untuk filter by category
            $table->index('is_available'); // Index untuk query topping available
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Rollback: Drop toppings table
     */
    public function down(): void
    {
        Schema::dropIfExists('toppings');
    }
};
