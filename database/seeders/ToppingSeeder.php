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
            ['name' => 'Bakso', 'price' => 4000],
        ];

        $drinkToppings = [];

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
