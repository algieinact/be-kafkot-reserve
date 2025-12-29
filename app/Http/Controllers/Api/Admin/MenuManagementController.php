<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuManagementController extends Controller
{
    /**
     * Get all menus (with filters)
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by availability
        if ($request->has('available_only') && $request->available_only) {
            $query->where('is_available', true);
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
     * Get single menu
     */
    public function show($id)
    {
        $menu = Menu::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $menu,
        ]);
    }

    /**
     * Create new menu
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:food,drink,dessert',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_available' => 'boolean',
        ]);

        try {
            $data = [
                'menu_name' => $request->menu_name,
                'description' => $request->description,
                'price' => $request->price,
                'category' => $request->category,
                'is_available' => $request->is_available ?? true,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'menu-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('menu-images', $filename, 'public');
                $data['image_url'] = $path;
            }

            $menu = Menu::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Menu created successfully',
                'data' => $menu,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create menu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update menu
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'menu_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:food,drink,dessert',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_available' => 'boolean',
        ]);

        try {
            $data = [
                'menu_name' => $request->menu_name,
                'description' => $request->description,
                'price' => $request->price,
                'category' => $request->category,
                'is_available' => $request->is_available ?? $menu->is_available,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($menu->image_url) {
                    Storage::disk('public')->delete($menu->image_url);
                }

                $file = $request->file('image');
                $filename = 'menu-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('menu-images', $filename, 'public');
                $data['image_url'] = $path;
            }

            $menu->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Menu updated successfully',
                'data' => $menu,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update menu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete menu
     */
    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id);

            // Delete image if exists
            if ($menu->image_url) {
                Storage::disk('public')->delete($menu->image_url);
            }

            $menu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Menu deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete menu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle menu availability
     */
    public function toggleAvailability($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->is_available = !$menu->is_available;
            $menu->save();

            return response()->json([
                'success' => true,
                'message' => 'Menu availability updated',
                'data' => $menu,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update availability: ' . $e->getMessage(),
            ], 500);
        }
    }
}
