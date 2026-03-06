<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'users' => User::all(),
        ]);
    }

    /**
     * PUT /products/{product}/images
     * Upload Images
     */
    public function uploadImages(Request $request, User $user)
    {
        /*
    | UPDATE IMAGE
    */
        if ($request->hasFile('image')) {

            if ($user->image) {
                $old = str_replace('/storage/', '', $user->image);
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('image')
                ->store('', 'public');

            $user->image = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Images synced',
            'user' => $user->fresh()
        ]);
    }

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

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
