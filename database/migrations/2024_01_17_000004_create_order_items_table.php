<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Table: order_items
     * Purpose: Detail item/produk dalam satu pesanan
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            // Cascade delete: Jika order dihapus, item ikut terhapus
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // Restrict delete: Produk tidak bisa dihapus jika sudah pernah dipesan
            // Gunakan soft delete di table products sebagai solusi
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict');

            // Transaction Details
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2); // Harga saat transaksi (Snapshot)

            $table->timestamps();

            // Indexes otomatis dibuat oleh foreignId(), tapi bisa ditambah compound index jika perlu
            // $table->index(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
