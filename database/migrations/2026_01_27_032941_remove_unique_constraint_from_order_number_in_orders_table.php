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
        Schema::table('orders', function (Blueprint $table) {
            // Drop unique constraint to allow daily reset (e.g. 001 for today and tomorrow)
            $table->dropUnique(['order_number']);
            
            // Add index for faster lookup with date
            // We use raw SQL for composite index if needed, but simple index on order_number is fine
            // or we rely on existing created_at index.
            // Let's just index order_number for performance
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_number']);
            $table->unique('order_number');
        });
    }
};
