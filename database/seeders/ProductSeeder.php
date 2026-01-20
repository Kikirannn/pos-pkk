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
            ['name' => 'Nasi Goreng', 'price' => 15000, 'desc' => 'Nasi goreng spesial dengan bumbu rahasia kantin.'],
            ['name' => 'Mie Ayam', 'price' => 12000, 'desc' => 'Mie ayam kenyal dengan topping ayam kecap gurih.'],
            ['name' => 'Ayam Geprek', 'price' => 18000, 'desc' => 'Ayam goreng crispy dengan sambal bawang pedas.'],
            ['name' => 'Nasi Uduk', 'price' => 13000, 'desc' => 'Nasi uduk wangi lengkap dengan bihun dan orek tempe.'],
            ['name' => 'Bakso', 'price' => 15000, 'desc' => 'Bakso sapi asli dengan kuah kaldu segar.'],
            ['name' => 'Soto Ayam', 'price' => 14000, 'desc' => 'Soto ayam kuah kuning segar dengan koya.'],
            ['name' => 'Mie Goreng', 'price' => 12000, 'desc' => 'Mie goreng jawa dengan sayuran segar.'],
            ['name' => 'Nasi Kuning', 'price' => 13000, 'desc' => 'Nasi kuning harum dengan lauk lengkap.'],
            ['name' => 'Ayam Bakar', 'price' => 20000, 'desc' => 'Ayam bakar manis gurih dengan lalapan segar.'],
            ['name' => 'Sate Ayam', 'price' => 18000, 'desc' => 'Sate ayam madura dengan bumbu kacang kental.'],
            ['name' => 'Pecel Lele', 'price' => 17000, 'desc' => 'Lele goreng garing dengan sambal terasi nikmat.'],
            ['name' => 'Capcay', 'price' => 15000, 'desc' => 'Tumis aneka sayuran sehat dan segar.'],
        ];

        $drinks = [
            ['name' => 'Es Teh', 'price' => 5000, 'desc' => 'Teh manis dingin menyegarkan.'],
            ['name' => 'Es Jeruk', 'price' => 7000, 'desc' => 'Perasan jeruk asli dengan es batu.'],
            ['name' => 'Kopi Hitam', 'price' => 8000, 'desc' => 'Kopi hitam panas, kuat dan beraroma.'],
            ['name' => 'Kopi Susu', 'price' => 10000, 'desc' => 'Kopi susu kekinian yang creamy.'],
            ['name' => 'Jus Alpukat', 'price' => 12000, 'desc' => 'Jus alpukat kental dengan susu coklat.'],
            ['name' => 'Jus Mangga', 'price' => 12000, 'desc' => 'Jus mangga harum manis segar.'],
            ['name' => 'Milkshake Coklat', 'price' => 15000, 'desc' => 'Minuman susu rasa coklat yang rich.'],
            ['name' => 'Es Teh Manis', 'price' => 5000, 'desc' => 'Teh manis klasik favorit semua orang.'],
            ['name' => 'Air Mineral', 'price' => 3000, 'desc' => 'Air mineral botol dingin.'],
            ['name' => 'Lemon Tea', 'price' => 8000, 'desc' => 'Teh dengan perasan lemon segar.'],
        ];

        foreach ($foods as $food) {
            Product::create([
                'name' => $food['name'],
                'category' => 'makanan',
                'price' => $food['price'],
                'image' => 'https://via.placeholder.com/300x200?text=' . urlencode($food['name']),
                'description' => $food['desc'],
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
                'is_available' => true,
            ]);
        }
    }
}
