<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tables')->insert([
            [
                'table_type_id' => 1,
                'table_number' => 'A01',
                'capacity' => 4,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_type_id' => 1,
                'table_number' => 'A02',
                'capacity' => 6,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_type_id' => 2,
                'table_number' => 'B01',
                'capacity' => 4,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_type_id' => 3,
                'table_number' => 'V01',
                'capacity' => 8,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
