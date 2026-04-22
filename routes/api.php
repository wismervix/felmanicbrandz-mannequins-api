<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API working'
    ]);
});

Route::get('/reset-admin-password', function () {
    try {
        $user = User::where('email', 'isaacosrael011@gmail.com')->first();

        if (!$user) {
            $user = User::create([
                'first_name' => 'Admin',
                'last_name'  => 'User',
                'age'        => 30,
                'gender'     => 'male',
                'email'      => 'isaacosrael011@gmail.com',
                'username'   => 'admin',
                'password'   => Hash::make('12345678'),
                'birthDate'  => '1995-01-01',
                'image'      => 'default.png',
                'role'       => 'admin',
                'address'    => 'N/A',
                'city'       => 'N/A',
                'state'      => 'N/A',
                'country'    => 'N/A',
            ]);

            return 'Admin user created!';
        }

        $user->password = Hash::make('12345678');
        $user->save();

        return 'Admin password reset!';
    } catch (\Exception $e) {
        return $e->getMessage(); // 👈 this exposes the real error
    }
});

Route::get('/routes-check', function () {
    return collect(Route::getRoutes())->map(function ($route) {
        return $route->uri();
    });
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Public guest products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Route::prefix('/admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
Route::middleware(['auth:sanctum', 'admin'])->group(function () {


    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::put('/users/{id}', 'update');
        Route::delete('/users/{id}', 'destroy');
        Route::post('/users/{user}/images', 'uploadImages');
    });

    Route::controller(ProductController::class)->group(function () {
        // Route::get('/products', 'index');
        // Route::get('/products/{id}', 'show');
        Route::post('/products/{product}/images', 'uploadImages');
        Route::post('/products', 'store');
        Route::put('/products/{id}', 'update');
        Route::delete('/products/{id}', 'destroy');
    });
});

// Route::middleware('auth')->group(function () {

//     Route::get('/admin/products', function () {
//         return 'Admin only';
//     });
// });

// Route::get('/admin', [AuthController::class, 'login']);
