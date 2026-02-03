<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foods = [
            ['name' => 'Mie Ayam', 'price' => 12000, 'desc' => 'Mie ayam kenyal dengan topping ayam kecap gurih.'],
            ['name' => 'Basreng', 'price' => 10000, 'desc' => 'Bakso goreng renyah dengan bumbu pedas mantap.'],
        ];

        $drinks = [
            ['name' => 'Lemon Tea', 'price' => 8000, 'desc' => 'Teh dengan perasan lemon segar.'],
        ];

        foreach ($foods as $food) {
            Product::create([
                'name' => $food['name'],
                'category' => 'makanan',
                'price' => $food['price'],
                'image' => 'https://via.placeholder.com/300x200?text=' . urlencode($food['name']),
                'description' => $food['desc'],
                'stock' => 50,
                'is_available' => true,
            ]);
        }

        foreach ($drinks as $drink) {
            Product::create([
                'name' => $drink['name'],
                'category' => 'minuman',
                'price' => $drink['price'],
                'image' => 'https://via.placeholder.com/300x200?text=' . urlencode($drink['name']),
                'description' => $drink['desc'],
                'stock' => 50,
                'is_available' => true,
            ]);
        }
    }
}
