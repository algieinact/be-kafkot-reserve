<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Selamat Datang di Kafkot',
                'subtitle' => 'Nikmati kopi premium dan suasana yang nyaman',
                'image_url' => 'https://images.unsplash.com/photo-1511920170033-f8396924c348?w=1200&h=600&fit=crop',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Menu Spesial Hari Ini',
                'subtitle' => 'Coba menu favorit pelanggan kami',
                'image_url' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1200&h=600&fit=crop',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Reservasi Sekarang',
                'subtitle' => 'Dapatkan meja terbaik untuk acara spesial Anda',
                'image_url' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=1200&h=600&fit=crop',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
