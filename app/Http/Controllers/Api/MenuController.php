<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Get all available menus with filters
     */
    public function index(Request $request)
    {
        $query = Menu::where('is_available', true);

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('menu_name', 'like', '%' . $request->search . '%');
        }

        $menus = $query->orderBy('category')->orderBy('menu_name')->get();

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    /**
     * Get single menu detail with variations
     */
    public function show($id)
    {
        $menu = Menu::with('variationGroups.options')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $menu,
        ]);
    }

    /**
     * Assign variation groups to a menu
     */
    public function assignVariations(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'variation_group_ids' => 'required|array',
            'variation_group_ids.*' => 'exists:variation_groups,id',
        ]);

        $menu->variationGroups()->sync($request->variation_group_ids);

        return response()->json([
            'success' => true,
            'message' => 'Variations assigned successfully',
            'data' => $menu->load('variationGroups.options'),
        ]);
    }

    /**
     * Remove a variation group from a menu
     */
    public function removeVariation($menuId, $groupId)
    {
        $menu = Menu::findOrFail($menuId);
        $menu->variationGroups()->detach($groupId);

        return response()->json([
            'success' => true,
            'message' => 'Variation removed successfully',
        ]);
    }
}
