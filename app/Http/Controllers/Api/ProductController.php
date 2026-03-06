<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'products' => Product::all(),
        ]);
    }

    /**
     * GET /api/products/{id}
     * Show single product
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * POST /api/products
     * Create product
     */
    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    /**
     * PUT /products/{product}/images
     * Upload Images
     */
    public function uploadImages(Request $request, Product $product)
    {
        $images = $product->images ?? [];

        /*
    | DELETE REMOVED IMAGES
    */
        if ($request->has('removedImages')) {

            foreach ($request->removedImages as $removed) {
                Storage::disk('public')->delete($removed);
            }

            $images = array_values(array_filter(
                $images,
                fn($img) => !in_array($img, $request->removedImages)
            ));
        }

        /*
    | UPDATE THUMBNAIL
    */
        if ($request->hasFile('thumbnail')) {

            if ($product->thumbnail) {
                $old = str_replace('/storage/', '', $product->thumbnail);
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('thumbnail')
                ->store('', 'public');

            $product->thumbnail = $path;
        }

        /*
    | ADD NEW IMAGES
    */
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $file) {

                $path = $file->store('', 'public');

                $images[] = $path;
            }
        }

        $product->images = array_values($images);
        $product->save();

        return response()->json([
            'message' => 'Images synced',
            'product' => $product->fresh()
        ]);
    }

    /**
     * PUT /api/products/{id}
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $this->validateProduct($request, true);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Shared validation logic
     */
    private function validateProduct(Request $request, $isUpdate = false)
    {
        $rules = [
            'title' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => $isUpdate ? 'sometimes|required|numeric|min:0' : 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'stock' => 'nullable|integer|min:0',
            'brand' => 'nullable|string|max:100',
            'sku' => 'nullable|string|max:50',
            'weight' => 'nullable|integer|min:0',
            'warranty_information' => 'nullable|string',
            'shipping_information' => 'nullable|string',
            'availability_status' => 'nullable|string',
            'return_policy' => 'nullable|string',
            'minimum_order_quantity' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'dimensions' => 'nullable|array',
            'reviews' => 'nullable|array',
            'meta' => 'nullable|array',
            'thumbnail' => 'nullable|string',
        ];

        return $request->validate($rules);
    }
}
