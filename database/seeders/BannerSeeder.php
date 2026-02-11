<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Welcome to Kafkot',
                'subtitle' => 'Experience the best coffee in town',
                'image_url' => 'https://res.cloudinary.com/dsev8bqmu/image/upload/v1770340309/kafkot/banners/banner-1770340301.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Freshly Brewed',
                'subtitle' => 'Start your day with a perfect cup',
                'image_url' => 'https://res.cloudinary.com/dsev8bqmu/image/upload/v1770340345/kafkot/banners/banner-1770340344.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Delicious Pastries',
                'subtitle' => 'Pair your coffee with our homemade treats',
                'image_url' => 'https://res.cloudinary.com/dsev8bqmu/image/upload/v1770340372/kafkot/banners/banner-1770340371.jpg',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
