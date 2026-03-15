<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API working'
    ]);
});

// Route::get('/reset-admin-password', function () {
//     $user = \App\Models\User::where('email', 'isaacosrael011@gmail.com')->first();
//     $user->password = Hash::make('12345678');
//     $user->save();
//     return 'Admin password reset!';
// });

// Route::get('/routes-check', function () {
//     return collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function ($route) {
//         return $route->uri();
//     });
// });

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::prefix('/admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
Route::middleware(['auth:sanctum', 'admin'])->group(function () {


    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::put('/users/{id}', 'update');
        Route::delete('/users/{id}', 'destroy');
        Route::post('/users/{user}/images', 'uploadImages');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/products/{id}', 'show');
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
