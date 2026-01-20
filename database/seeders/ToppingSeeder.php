<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foodToppings = [
            ['name' => 'Keju', 'price' => 5000],
            ['name' => 'Telur', 'price' => 3000],
            ['name' => 'Kornet', 'price' => 5000],
            ['name' => 'Sosis', 'price' => 4000],
            ['name' => 'Bakso', 'price' => 4000],
            ['name' => 'Ayam Suwir', 'price' => 6000],
            ['name' => 'Sayur', 'price' => 2000],
            ['name' => 'Sambal Extra', 'price' => 1000],
        ];

        $drinkToppings = [
            ['name' => 'Boba', 'price' => 5000],
            ['name' => 'Jelly', 'price' => 3000],
            ['name' => 'Extra Shot Espresso', 'price' => 5000],
            ['name' => 'Whipped Cream', 'price' => 4000],
            ['name' => 'Cincau', 'price' => 3000],
            ['name' => 'Brown Sugar', 'price' => 3000],
        ];

        foreach ($foodToppings as $topping) {
            Topping::create([
                'name' => $topping['name'],
                'price' => $topping['price'],
                'category' => 'makanan',
                'is_available' => true,
            ]);
        }

        foreach ($drinkToppings as $topping) {
            Topping::create([
                'name' => $topping['name'],
                'price' => $topping['price'],
                'category' => 'minuman',
                'is_available' => true,
            ]);
        }
    }
}
