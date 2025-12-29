<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('table_types')->insert([
            [
                'type_name' => 'Indoor',
                'description' => 'Meja di dalam ruangan dengan suasana nyaman',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_name' => 'Outdoor',
                'description' => 'Meja di luar ruangan dengan pemandangan alam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_name' => 'VIP',
                'description' => 'Meja VIP dengan fasilitas premium dan privasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
