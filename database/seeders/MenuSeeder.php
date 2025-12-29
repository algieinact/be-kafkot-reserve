<?php

namespace Database\Seeders;

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
            // Coffee
            [
                'menu_name' => 'Espresso',
                'description' => 'Classic Italian espresso dengan rasa bold dan rich',
                'price' => 25000.00,
                'category' => 'drink',
                'image_url' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Cappuccino',
                'description' => 'Espresso dengan steamed milk dan foam yang creamy',
                'price' => 30000.00,
                'category' => 'drink',
                'image_url' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Caffe Latte',
                'description' => 'Espresso dengan lebih banyak steamed milk, perfect untuk pemula',
                'price' => 32000.00,
                'category' => 'drink',
                'image_url' => 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Americano',
                'description' => 'Espresso dengan hot water, strong dan smooth',
                'price' => 28000.00,
                'category' => 'drink',
                'image_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Mocha',
                'description' => 'Kombinasi espresso, chocolate, dan steamed milk',
                'price' => 35000.00,
                'category' => 'drink',
                'image_url' => 'https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Non-Coffee Drinks
            [
                'menu_name' => 'Matcha Latte',
                'description' => 'Japanese matcha premium dengan susu hangat',
                'price' => 33000.00,
                'category' => 'drink',
                'image_url' => 'https://images.unsplash.com/photo-1717603545758-88cc454db69b?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Chocolate',
                'description' => 'Rich hot chocolate dengan whipped cream',
                'price' => 30000.00,
                'category' => 'drink',
                'image_url' => 'https://images.unsplash.com/photo-1542990253-a781e04c0082?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Lemon Tea',
                'description' => 'Teh segar dengan perasan lemon alami',
                'price' => 22000.00,
                'category' => 'drink',
                'image_url' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Food
            [
                'menu_name' => 'Croissant',
                'description' => 'French butter croissant yang flaky dan buttery',
                'price' => 28000.00,
                'category' => 'food',
                'image_url' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Sandwich Club',
                'description' => 'Triple decker sandwich dengan chicken, bacon, lettuce, dan tomato',
                'price' => 45000.00,
                'category' => 'food',
                'image_url' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Pasta Carbonara',
                'description' => 'Creamy carbonara dengan bacon dan parmesan',
                'price' => 52000.00,
                'category' => 'food',
                'image_url' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Nasi Goreng Special',
                'description' => 'Nasi goreng dengan telur, ayam, dan kerupuk',
                'price' => 38000.00,
                'category' => 'food',
                'image_url' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Desserts
            [
                'menu_name' => 'Tiramisu',
                'description' => 'Italian dessert dengan coffee-soaked ladyfingers dan mascarpone',
                'price' => 42000.00,
                'category' => 'dessert',
                'image_url' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Chocolate Brownie',
                'description' => 'Rich chocolate brownie dengan vanilla ice cream',
                'price' => 38000.00,
                'category' => 'dessert',
                'image_url' => 'https://images.unsplash.com/photo-1607920591413-4ec007e70023?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu_name' => 'Cheesecake',
                'description' => 'Creamy New York style cheesecake dengan berry compote',
                'price' => 40000.00,
                'category' => 'dessert',
                'image_url' => 'https://images.unsplash.com/photo-1524351199678-941a58a3df50?w=400&h=300&fit=crop',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
