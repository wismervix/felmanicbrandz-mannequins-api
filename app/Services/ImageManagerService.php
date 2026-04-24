<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryService;

class ImageManagerService
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    /**
     * ✅ Handle single image (e.g. User avatar)
     */
    public function updateSingleImage($model, $file, string $folder, string $column = 'image'): array
    {
        $newPublicId = null;
        $oldPublicId = is_array($model->{$column})
            ? ($model->{$column}['public_id'] ?? null)
            : null;

        try {
            // ✅ 1. Upload first
            $uploaded = $this->cloudinary->upload($file, $folder);
            $newPublicId = $uploaded['public_id'];

            DB::beginTransaction();

            // ✅ 2. Save DB
            $model->{$column} = [
                'url' => $uploaded['secure_url'],
                'public_id' => $uploaded['public_id'],
            ];

            $model->save();

            DB::commit();

            // ✅ 3. Delete old AFTER commit
            if ($oldPublicId) {
                $this->safeDelete($oldPublicId);
            }

            return [
                'success' => true,
                'model' => $model->fresh()
            ];
        } catch (\Exception $e) {

            DB::rollBack();

            // 🔥 cleanup NEW upload only
            if ($newPublicId) {
                $this->safeDelete($newPublicId);
            }

            Log::error('Single image update failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Upload failed'
            ];
        }
    }

    /**
     * ✅ Handle product images (thumbnail + gallery)
     */
    public function updateProductImages($product, $request): array
    {
        $uploadedPublicIds = [];
        $toDeleteAfterCommit = [];

        try {
            $images = $product->images ?? [];

            // ✅ 1. Prepare removals (no deletion yet)
            $removedImages = $request->input('removedImages', []);
            $existingPublicIds = collect($images)->pluck('public_id')->toArray();

            $validRemovals = array_intersect($existingPublicIds, $removedImages);

            $images = array_values(array_filter(
                $images,
                fn($img) => !in_array($img['public_id'], $validRemovals)
            ));

            $toDeleteAfterCommit = array_merge($toDeleteAfterCommit, $validRemovals);

            // ✅ 2. Upload thumbnail FIRST
            if ($request->hasFile('thumbnail')) {

                $oldThumbnailId = $product->thumbnail['public_id'] ?? null;

                $uploaded = $this->cloudinary->upload(
                    $request->file('thumbnail'),
                    'products/thumbnails'
                );

                $uploadedPublicIds[] = $uploaded['public_id'];

                $product->thumbnail = [
                    'url' => $uploaded['secure_url'],
                    'public_id' => $uploaded['public_id'],
                ];

                if ($oldThumbnailId) {
                    $toDeleteAfterCommit[] = $oldThumbnailId;
                }
            }

            // ✅ 3. Upload gallery images FIRST
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {

                    $uploaded = $this->cloudinary->upload($file, 'products');

                    $uploadedPublicIds[] = $uploaded['public_id'];

                    $images[] = [
                        'url' => $uploaded['secure_url'],
                        'public_id' => $uploaded['public_id'],
                    ];
                }
            }

            // ✅ 4. DB transaction (FAST)
            DB::beginTransaction();

            $product->images = array_values($images);
            $product->save();

            DB::commit();

            // ✅ 5. Delete old AFTER commit
            foreach ($toDeleteAfterCommit as $publicId) {
                $this->safeDelete($publicId);
            }

            return [
                'success' => true,
                'product' => $product->fresh()
            ];
        } catch (\Exception $e) {

            DB::rollBack();

            // 🔥 cleanup NEW uploads only
            foreach ($uploadedPublicIds as $publicId) {
                $this->safeDelete($publicId);
            }

            Log::error('Product image update failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Upload failed'
            ];
        }
    }

    /**
     * ✅ Safe delete wrapper (prevents crashes)
     */
    protected function safeDelete(string $publicId): void
    {
        $result = $this->cloudinary->delete($publicId);

        if (!$result) {
            Log::warning('Cloudinary delete failed', [
                'public_id' => $publicId,
            ]);
        }
    }
}
