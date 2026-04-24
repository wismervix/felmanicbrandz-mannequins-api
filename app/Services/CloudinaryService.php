<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $url = config('cloudinary.cloud_url');

        if (!$url) {
            throw new \Exception('Cloudinary config missing');
        }

        $this->cloudinary = new Cloudinary($url);
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
