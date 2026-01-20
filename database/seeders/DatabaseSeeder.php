<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in correct order
        $this->call([
            ProductSeeder::class,
            ToppingSeeder::class,
        ]);

        // Future seeders can be added here
        // \App\Models\User::factory(10)->create();
    }
}
