<?php

namespace Database\Seeders;

use App\Models\VariationGroup;
use App\Models\VariationOption;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class VariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Size variation group
        $sizeGroup = VariationGroup::create([
            'name' => 'Ukuran',
            'type' => 'single_choice',
            'is_required' => true,
            'min_selections' => 1,
            'max_selections' => 1,
        ]);

        // Create size options
        VariationOption::create([
            'variation_group_id' => $sizeGroup->id,
            'name' => 'Regular',
            'price_adjustment' => 0,
            'is_default' => true,
            'order' => 1,
        ]);

        VariationOption::create([
            'variation_group_id' => $sizeGroup->id,
            'name' => 'Large',
            'price_adjustment' => 5000,
            'is_default' => false,
            'order' => 2,
        ]);

        // Create Temperature variation group
        $tempGroup = VariationGroup::create([
            'name' => 'Suhu',
            'type' => 'single_choice',
            'is_required' => true,
            'min_selections' => 1,
            'max_selections' => 1,
        ]);

        // Create temperature options
        VariationOption::create([
            'variation_group_id' => $tempGroup->id,
            'name' => 'Hot',
            'price_adjustment' => 0,
            'is_default' => true,
            'order' => 1,
        ]);

        VariationOption::create([
            'variation_group_id' => $tempGroup->id,
            'name' => 'Iced',
            'price_adjustment' => 3000,
            'is_default' => false,
            'order' => 2,
        ]);

        // Create Milk variation group
        $milkGroup = VariationGroup::create([
            'name' => 'Jenis Susu',
            'type' => 'single_choice',
            'is_required' => false,
            'min_selections' => 0,
            'max_selections' => 1,
        ]);

        // Create milk options
        VariationOption::create([
            'variation_group_id' => $milkGroup->id,
            'name' => 'Regular Milk',
            'price_adjustment' => 0,
            'is_default' => true,
            'order' => 1,
        ]);

        VariationOption::create([
            'variation_group_id' => $milkGroup->id,
            'name' => 'Oat Milk',
            'price_adjustment' => 7000,
            'is_default' => false,
            'order' => 2,
        ]);

        VariationOption::create([
            'variation_group_id' => $milkGroup->id,
            'name' => 'Almond Milk',
            'price_adjustment' => 8000,
            'is_default' => false,
            'order' => 3,
        ]);

        VariationOption::create([
            'variation_group_id' => $milkGroup->id,
            'name' => 'Soy Milk',
            'price_adjustment' => 6000,
            'is_default' => false,
            'order' => 4,
        ]);

        // Create Extra Toppings variation group
        $toppingGroup = VariationGroup::create([
            'name' => 'Extra Topping',
            'type' => 'multiple_choice',
            'is_required' => false,
            'min_selections' => 0,
            'max_selections' => 5,
        ]);

        // Create topping options
        VariationOption::create([
            'variation_group_id' => $toppingGroup->id,
            'name' => 'Extra Shot Espresso',
            'price_adjustment' => 8000,
            'is_default' => false,
            'order' => 1,
        ]);

        VariationOption::create([
            'variation_group_id' => $toppingGroup->id,
            'name' => 'Whipped Cream',
            'price_adjustment' => 5000,
            'is_default' => false,
            'order' => 2,
        ]);

        VariationOption::create([
            'variation_group_id' => $toppingGroup->id,
            'name' => 'Caramel Drizzle',
            'price_adjustment' => 5000,
            'is_default' => false,
            'order' => 3,
        ]);

        VariationOption::create([
            'variation_group_id' => $toppingGroup->id,
            'name' => 'Chocolate Chips',
            'price_adjustment' => 6000,
            'is_default' => false,
            'order' => 4,
        ]);

        // Attach variations to drink menus
        $drinkMenus = Menu::where('category', 'drink')->get();
        foreach ($drinkMenus as $menu) {
            $menu->variationGroups()->attach([
                $sizeGroup->id,
                $tempGroup->id,
                $milkGroup->id,
                $toppingGroup->id,
            ]);
        }
    }
}
