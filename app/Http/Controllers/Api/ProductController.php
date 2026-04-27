<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Services\ImageManagerService;

#[OA\Tag(name: "Products", description: "Product management endpoints")]
class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "List all products",
        description: "Get a list of all products",
        tags: ["Products"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "products", type: "array", items: new OA\Items(type: "object"))
                    ]
                )
            )
        ]
    )]
    public function index()
    {
        return response()->json([
            'products' => Product::all(),
        ]);
    }

    #[OA\Get(
        path: "/api/products/{id}",
        summary: "Get product by ID",
        description: "Retrieve a single product by its ID",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Product found"),
            new OA\Response(response: 404, description: "Product not found")
        ]
    )]
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'product' => $product
        ]);
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Create product",
        description: "Create a new product",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "price"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Mannequin Stand"),
                    new OA\Property(property: "description", type: "string", nullable: true),
                    new OA\Property(property: "category", type: "string", nullable: true),
                    new OA\Property(property: "price", type: "number", format: "float", example: 99.99),
                    new OA\Property(property: "discount_percentage", type: "number", nullable: true),
                    new OA\Property(property: "rating", type: "number", nullable: true),
                    new OA\Property(property: "stock", type: "integer", nullable: true),
                    new OA\Property(property: "brand", type: "string", nullable: true),
                    new OA\Property(property: "sku", type: "string", nullable: true),
                    new OA\Property(property: "weight", type: "integer", nullable: true),
                    new OA\Property(property: "warranty_information", type: "string", nullable: true),
                    new OA\Property(property: "shipping_information", type: "string", nullable: true),
                    new OA\Property(property: "availability_status", type: "string", nullable: true),
                    new OA\Property(property: "return_policy", type: "string", nullable: true),
                    new OA\Property(property: "minimum_order_quantity", type: "integer", nullable: true),
                    new OA\Property(property: "tags", type: "array", items: new OA\Items(type: "string"), nullable: true),
                    new OA\Property(property: "images", type: "array", items: new OA\Items(type: "string"), nullable: true),
                    new OA\Property(property: "dimensions", type: "object", nullable: true),
                    new OA\Property(property: "reviews", type: "array", items: new OA\Items(type: "object"), nullable: true),
                    new OA\Property(property: "meta", type: "object", nullable: true),
                    new OA\Property(property: "thumbnail", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Product created"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    #[OA\Post(
        path: "/api/products/{product}/images",
        summary: "Upload product images",
        description: "Upload or update images for a product",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "product", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "thumbnail", type: "string", format: "binary"),
                        new OA\Property(property: "images[]", type: "array", items: new OA\Items(type: "string", format: "binary")),
                        new OA\Property(property: "removedImages", type: "array", items: new OA\Items(type: "string"))
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Images synced")
        ]
    )]
    public function uploadImages(Request $request, Product $product, ImageManagerService $imageManager)
    {
        $request->validate([
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'removedImages' => 'nullable|array',
            'removedImages.*' => 'string'
        ]);

        $result = $imageManager->updateProductImages($product, $request);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 500);
        }

        return response()->json([
            'message' => 'Images synced',
            'product' => $result['product']
        ]);
    }

    #[OA\Put(
        path: "/api/products/{id}",
        summary: "Update product",
        description: "Update an existing product",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "description", type: "string", nullable: true),
                    new OA\Property(property: "price", type: "number", format: "float"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Product updated"),
            new OA\Response(response: 404, description: "Product not found")
        ]
    )]
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $this->validateProduct($request, true);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail');
        } elseif ($request->filled('thumbnail')) {
            // existing thumbnail object sent from frontend
            $validated['thumbnail'] = json_decode($request->thumbnail, true);
        } else {
            unset($validated['thumbnail']);
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    #[OA\Delete(
        path: "/api/products/{id}",
        summary: "Delete product",
        description: "Delete a product by ID",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Product deleted"),
            new OA\Response(response: 404, description: "Product not found")
        ]
    )]
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

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
            'thumbnail' => 'nullable',
            // 'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        return $request->validate($rules);
    }
}
