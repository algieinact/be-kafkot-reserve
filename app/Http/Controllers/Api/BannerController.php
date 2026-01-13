<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    /**
     * Get active banners for public display
     */
    public function index()
    {
        $banners = Banner::active()->get();

        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }

    /**
     * Get all banners for admin (including inactive)
     */
    public function adminIndex()
    {
        $banners = Banner::withoutGlobalScope('ordered')
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }

    /**
     * Store a newly created banner
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Upload image to Cloudinary using CloudinaryService
            $imageUrl = '';

            if ($request->hasFile('image')) {
                $cloudinary = new CloudinaryService();
                $publicId = 'banner-' . time(); // Unique ID for banner

                $uploadResult = $cloudinary->uploadImage(
                    $request->file('image'),
                    'kafkot/banners', // Folder specifically for banners
                    $publicId
                );

                $imageUrl = $uploadResult['secure_url'];
            } else {
                throw new \Exception('Image file is required');
            }

            $banner = Banner::create([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'image_url' => $imageUrl,
                'order' => $request->order ?? 0,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Banner created successfully',
                'data' => $banner,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to upload image',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified banner
     */
    public function show($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'error' => 'Banner not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $banner,
        ]);
    }

    /**
     * Update the specified banner
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'error' => 'Banner not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'order' => $request->order ?? $banner->order,
                'is_active' => $request->is_active ?? $banner->is_active,
            ];

            // Upload new image if provided
            if ($request->hasFile('image')) {
                $cloudinary = new CloudinaryService();

                // Delete old image if exists and valid Cloudinary URL
                if ($banner->image_url && str_contains($banner->image_url, 'cloudinary')) {
                    $oldPublicId = $cloudinary->extractPublicId($banner->image_url);
                    if ($oldPublicId) {
                        $cloudinary->deleteImage($oldPublicId);
                    }
                }

                $publicId = 'banner-' . $banner->id . '-' . time();

                $uploadResult = $cloudinary->uploadImage(
                    $request->file('image'),
                    'kafkot/banners',
                    $publicId
                );

                $data['image_url'] = $uploadResult['secure_url'];
            }

            $banner->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Banner updated successfully',
                'data' => $banner,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update banner',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified banner
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'error' => 'Banner not found',
            ], 404);
        }

        // Delete image from Cloudinary if exists
        if ($banner->image_url && str_contains($banner->image_url, 'cloudinary')) {
            try {
                $cloudinary = new CloudinaryService();
                $publicId = $cloudinary->extractPublicId($banner->image_url);
                if ($publicId) {
                    $cloudinary->deleteImage($publicId);
                }
            } catch (\Exception $e) {
                // Log error but continue deletion of banner
                \Log::error('Failed to delete banner image from Cloudinary: ' . $e->getMessage());
            }
        }

        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully',
        ]);
    }
}
