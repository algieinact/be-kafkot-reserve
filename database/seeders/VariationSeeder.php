<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VariationGroup;
use App\Models\VariationOption;

class VariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Sugar Level (Single Choice, Required)
        $sugar = VariationGroup::create([
            'name' => 'Sugar Level',
            'type' => 'single_choice',
            'is_required' => true,
            'min_selections' => 1,
            'max_selections' => 1,
        ]);
        VariationOption::create(['variation_group_id' => $sugar->id, 'name' => 'Normal Sugar', 'is_default' => true, 'order' => 1]);
        VariationOption::create(['variation_group_id' => $sugar->id, 'name' => 'Less Sugar', 'order' => 2]);
        VariationOption::create(['variation_group_id' => $sugar->id, 'name' => 'Half Sugar', 'order' => 3]);
        VariationOption::create(['variation_group_id' => $sugar->id, 'name' => 'No Sugar', 'order' => 4]);

        // 2. Ice Level (Single Choice, Optional - defaults to Normal if not picked, often required for ice drinks)
        $ice = VariationGroup::create([
            'name' => 'Ice Level',
            'type' => 'single_choice',
            'is_required' => true,
            'min_selections' => 1,
            'max_selections' => 1,
        ]);
        VariationOption::create(['variation_group_id' => $ice->id, 'name' => 'Normal Ice', 'is_default' => true, 'order' => 1]);
        VariationOption::create(['variation_group_id' => $ice->id, 'name' => 'Less Ice', 'order' => 2]);
        VariationOption::create(['variation_group_id' => $ice->id, 'name' => 'No Ice', 'order' => 3]);
        VariationOption::create(['variation_group_id' => $ice->id, 'name' => 'Extra Ice', 'order' => 4]);

        // 3. Spice Level (Single Choice, Required for spicy foods)
        $spice = VariationGroup::create([
            'name' => 'Spice Level',
            'type' => 'single_choice',
            'is_required' => true,
            'min_selections' => 1,
            'max_selections' => 1,
        ]);
        VariationOption::create(['variation_group_id' => $spice->id, 'name' => 'Tidak Pedas', 'is_default' => true, 'order' => 1]);
        VariationOption::create(['variation_group_id' => $spice->id, 'name' => 'Sedang', 'order' => 2]);
        VariationOption::create(['variation_group_id' => $spice->id, 'name' => 'Pedas', 'order' => 3]);
        VariationOption::create(['variation_group_id' => $spice->id, 'name' => 'Pedas Banget', 'order' => 4]);

        // 4. Size (Single Choice, Required)
        $size = VariationGroup::create([
            'name' => 'Size',
            'type' => 'single_choice',
            'is_required' => true,
            'min_selections' => 1,
            'max_selections' => 1,
        ]);
        VariationOption::create(['variation_group_id' => $size->id, 'name' => 'Regular', 'is_default' => true, 'order' => 1]);
        VariationOption::create(['variation_group_id' => $size->id, 'name' => 'Large', 'price_adjustment' => 5000, 'order' => 2]);
    }
}
