<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee'],
            ['name' => 'Non-Coffee'],
            ['name' => 'Signature'],
            ['name' => 'Rice'],
            ['name' => 'Pasta'],
            ['name' => 'Fried Rice'],
            ['name' => 'Pizza'],
            ['name' => 'Western'],
            ['name' => 'Light Meals'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
