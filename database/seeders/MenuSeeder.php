<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menus')->insert([
            [
                'menu_name' => 'Nasi Goreng',
                'description' => 'Nasi goreng spesial dengan telur dan ayam',
                'price' => 25000.00,
                'category' => 'Makanan',
                'image_url' => null,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Ayam Bakar',
                'description' => 'Ayam bakar dengan bumbu rempah',
                'price' => 30000.00,
                'category' => 'Makanan',
                'image_url' => null,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Es Teh Manis',
                'description' => 'Teh manis dingin segar',
                'price' => 5000.00,
                'category' => 'Minuman',
                'image_url' => null,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Jus Jeruk',
                'description' => 'Jus jeruk segar tanpa gula tambahan',
                'price' => 15000.00,
                'category' => 'Minuman',
                'image_url' => null,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
