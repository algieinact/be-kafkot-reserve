<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Exception;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        // Initialize Cloudinary using CLOUDINARY_URL from .env
        $this->cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
    }

    /**
     * Upload an image to Cloudinary
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder Cloudinary folder name
     * @param string|null $publicId Optional custom public ID
     * @return array ['url' => string, 'public_id' => string, 'secure_url' => string]
     * @throws Exception
     */
    public function uploadImage($file, string $folder = 'kafkot', ?string $publicId = null): array
    {
        try {
            $options = [
                'folder' => $folder,
                'resource_type' => 'image',
                'overwrite' => false,
                'invalidate' => true,
            ];

            if ($publicId) {
                $options['public_id'] = $publicId;
            }

            // Upload to Cloudinary
            $uploadResult = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                $options
            );

            return [
                'url' => $uploadResult['url'],
                'secure_url' => $uploadResult['secure_url'],
                'public_id' => $uploadResult['public_id'],
                'format' => $uploadResult['format'],
                'width' => $uploadResult['width'],
                'height' => $uploadResult['height'],
                'bytes' => $uploadResult['bytes'],
            ];

        } catch (Exception $e) {
            throw new Exception('Cloudinary upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete an image from Cloudinary
     *
     * @param string $publicId The public ID of the image to delete
     * @return bool
     */
    public function deleteImage(string $publicId): bool
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return $result['result'] === 'ok';
        } catch (Exception $e) {
            // Log error but don't throw - deletion failure shouldn't break the flow
            \Log::warning('Cloudinary deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract public_id from Cloudinary URL
     *
     * @param string $url Cloudinary URL
     * @return string|null
     */
    public function extractPublicId(string $url): ?string
    {
        // Extract public_id from URL like: https://res.cloudinary.com/{cloud_name}/image/upload/{version}/{public_id}.{format}
        if (preg_match('/\/v\d+\/(.+)\.\w+$/', $url, $matches)) {
            return $matches[1];
        }

        // Fallback: try to extract from URL without version
        if (preg_match('/\/upload\/(.+)\.\w+$/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
