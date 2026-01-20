<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Table: orders
     * Purpose: Menyimpan data pesanan pelanggan (header)
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Order Identification
            $table->string('order_number', 10)->unique(); // Unik, misal: "ORD001"

            // Financial
            $table->decimal('total_price', 10, 2)->default(0); // Total transaksi

            // Workflow Status
            $table->enum('status', ['new', 'processing', 'done'])->default('new');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('status'); // Penting untuk filtering di Kitchen Display
            $table->index('created_at'); // Untuk filtering laporan harian
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
