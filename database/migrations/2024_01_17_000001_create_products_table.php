<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Table: products
     * Purpose: Menyimpan data menu makanan dan minuman kantin
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // Primary Key
            $table->id(); // bigIncrements (BIGINT UNSIGNED AUTO_INCREMENT)

            // Product Information
            $table->string('name', 100); // Nama produk (max 100 karakter)
            $table->enum('category', ['makanan', 'minuman']); // Kategori produk
            $table->decimal('price', 10, 2); // Harga (max 99,999,999.99)

            // Optional Fields
            $table->string('image', 255)->nullable(); // Path/URL foto produk
            $table->text('description')->nullable(); // Deskripsi produk

            // Status
            $table->boolean('is_available')->default(true); // Ketersediaan produk

            // Timestamps (created_at, updated_at)
            $table->timestamps();

            // Indexes untuk performance
            $table->index('category'); // Index untuk filter by category
            $table->index('is_available'); // Index untuk query produk available
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Rollback: Drop products table
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
