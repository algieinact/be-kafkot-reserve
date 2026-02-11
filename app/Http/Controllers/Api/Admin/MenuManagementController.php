<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

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
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_available' => 'boolean',
        ]);

        try {
            $data = [
                'menu_name' => $request->menu_name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'is_available' => $request->is_available ?? true,
            ];

            // Handle image upload to Cloudinary
            if ($request->hasFile('image')) {
                $cloudinary = new CloudinaryService();
                $publicId = 'menu-' . time();

                $uploadResult = $cloudinary->uploadImage(
                    $request->file('image'),
                    'kafkot/menus',
                    $publicId
                );

                $data['image_url'] = $uploadResult['secure_url'];
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
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_available' => 'boolean',
        ]);

        try {
            $data = [
                'menu_name' => $request->menu_name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'is_available' => $request->is_available ?? $menu->is_available,
            ];

            // Handle image upload to Cloudinary
            if ($request->hasFile('image')) {
                $cloudinary = new CloudinaryService();

                // Delete old image if exists and valid Cloudinary URL
                if ($menu->image_url && str_contains($menu->image_url, 'cloudinary')) {
                    $oldPublicId = $cloudinary->extractPublicId($menu->image_url);
                    if ($oldPublicId) {
                        $cloudinary->deleteImage($oldPublicId);
                    }
                }

                $publicId = 'menu-' . $menu->id . '-' . time();

                $uploadResult = $cloudinary->uploadImage(
                    $request->file('image'),
                    'kafkot/menus',
                    $publicId
                );

                $data['image_url'] = $uploadResult['secure_url'];
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

            // Delete image from Cloudinary if exists
            if ($menu->image_url && str_contains($menu->image_url, 'cloudinary')) {
                try {
                    $cloudinary = new CloudinaryService();
                    $publicId = $cloudinary->extractPublicId($menu->image_url);
                    if ($publicId) {
                        $cloudinary->deleteImage($publicId);
                    }
                } catch (\Exception $e) {
                    // Log error but continue deletion of menu
                    \Log::error('Failed to delete menu image from Cloudinary: ' . $e->getMessage());
                }
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
