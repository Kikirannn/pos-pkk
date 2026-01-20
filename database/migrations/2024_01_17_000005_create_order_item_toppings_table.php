<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Table: order_item_toppings
     * Purpose: Pivot table untuk menyimpan topping yang dipilih per item
     */
    public function up(): void
    {
        Schema::create('order_item_toppings', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            // Cascade: Jika item pesanan dihapus, topping associated juga hapus
            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->onDelete('cascade');

            // Restrict: Topping master tidak bisa dihapus sembarangan
            $table->foreignId('topping_id')
                ->constrained('toppings')
                ->onDelete('restrict');

            // Transaction Details
            $table->decimal('price', 10, 2); // Harga topping saat transaksi (Snapshot)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_toppings');
    }
};
