<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Users", description: "User management endpoints")]
class UserController extends Controller
// security: [["bearerAuth" => []]],
{
    #[OA\Get(
        path: "/api/users",
        summary: "List all users",
        description: "Get a list of all registered users",
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "users", type: "array", items: new OA\Items(type: "object"))
                    ]
                )
            )
        ]
    )]
    public function index()
    {
        return response()->json([
            'users' => User::all(),
        ]);
    }

    #[OA\Post(
        path: "/api/users/{user}/images",
        summary: "Upload user image",
        description: "Upload or update profile image for a user",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "user", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "image", type: "string", format: "binary")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Image synced")
        ]
    )]
    public function uploadImages(Request $request, User $user, CloudinaryService $cloudinary)
    {
        try {
            if ($request->hasFile('image')) {

                Log::info('File received', [
                    'name' => $request->file('image')->getClientOriginalName()
                ]);

                $uploaded = $cloudinary->upload($request->file('image'), 'users');

                Log::info('Cloudinary response', ['data' => $uploaded]);

                $user->image = $uploaded['secure_url'];
            }

            $user->save();

            return response()->json([
                'message' => 'Images synced',
                'user' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Upload failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }

        // if ($request->hasFile('image')) {


        //     // Upload to Cloudinary
        //     $uploaded = $cloudinary->upload($request->file('image'), 'users');

        //     // Save ONLY the URL
        //     $user->image = $uploaded['secure_url'];

        //     // if ($user->image) {
        //     //     $old = str_replace('/storage/', '', $user->image);
        //     //     Storage::disk('public')->delete($old);
        //     // }

        //     // $path = $request->file('image')->store('', 'public');
        //     // $user->image = $path;
        // }

        // $user->save();

        // return response()->json([
        //     'message' => 'Images synced',
        //     'user' => $user->fresh()
        // ]);
    }

    #[OA\Put(
        path: "/api/users/{id}",
        summary: "Update user",
        description: "Update an existing user",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["first_name", "last_name", "email", "username", "age", "gender", "role", "address", "city", "state", "country", "birthDate"],
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "username", type: "string", example: "johndoe"),
                    new OA\Property(property: "age", type: "integer", example: 30),
                    new OA\Property(property: "gender", type: "string", example: "male"),
                    new OA\Property(property: "role", type: "string", example: "admin"),
                    new OA\Property(property: "address", type: "string", example: "123 Main St"),
                    new OA\Property(property: "city", type: "string", example: "New York"),
                    new OA\Property(property: "state", type: "string", example: "NY"),
                    new OA\Property(property: "country", type: "string", example: "USA"),
                    new OA\Property(property: "birthDate", type: "string", format: "date", example: "1994-01-15"),
                    new OA\Property(property: "password", type: "string", format: "password", nullable: true),
                    new OA\Property(property: "image", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "User updated"),
            new OA\Response(response: 404, description: "User not found"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email',
            'username' => 'required|string|min:3',
            'age' => 'required|integer|min:0',
            'gender' => 'required|string',
            'role' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'birthDate' => 'required|date',
            'password' => ['nullable', Password::min(8)],
            'image' => 'nullable|string',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    #[OA\Delete(
        path: "/api/users/{id}",
        summary: "Delete user",
        description: "Delete a user by ID",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "User deleted"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
