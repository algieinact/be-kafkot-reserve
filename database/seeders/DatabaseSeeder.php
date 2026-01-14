<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TableTypeSeeder::class,
            TableSeeder::class,
            UserSeeder::class,
            MenuSeeder::class,
            BankAccountSeeder::class,
            BannerSeeder::class,
            VariationSeeder::class,
        ]);
    }
}
