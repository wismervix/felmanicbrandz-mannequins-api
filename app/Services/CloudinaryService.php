<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        // $this->cloudinary = new Cloudinary();

        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    public function upload($file, $folder = 'general')
    {
        return $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            ['folder' => $folder]
        );
    }

    public function delete($publicId)
    {
        try {
            return $this->cloudinary->uploadApi()->destroy($publicId);
        } catch (\Exception $e) {
            Log::error("Cloudinary delete failed", [
                'public_id' => $publicId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
