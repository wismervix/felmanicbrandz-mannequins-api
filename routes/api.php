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

// Route::get('/reset-admin-password', function () {
//     try {
//         $user = User::where('email', 'isaacosrael011@gmail.com')->first();

//         if (!$user) {
//             $user = User::create([
//                 'first_name' => 'Admin',
//                 'last_name'  => 'User',
//                 'age'        => 30,
//                 'gender'     => 'male',
//                 'email'      => 'isaacosrael011@gmail.com',
//                 'username'   => 'admin',
//                 'password'   => Hash::make('12345678'),
//                 'birthDate'  => '1995-01-01',
//                 'image'      => 'default.png',
//                 'role'       => 'admin',
//                 'address'    => 'N/A',
//                 'city'       => 'N/A',
//                 'state'      => 'N/A',
//                 'country'    => 'N/A',
//             ]);

//             return 'Admin user created!';
//         }

//         $user->password = Hash::make('12345678');
//         $user->save();

//         return 'Admin password reset!';
//     } catch (\Exception $e) {
//         return $e->getMessage(); // 👈 this exposes the real error
//     }
// });

Route::get('/routes-check', function () {
    return collect(Route::getRoutes())->map(function ($route) {
        return $route->uri();
    });
});

Route::post('/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware('auth:sanctum');

// Public guest products
Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('admin.products.show');

// Protected admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('admin.users');
        Route::put('/users/{id}', 'update')->name('admin.users.update');
        Route::delete('/users/{id}', 'destroy')->name('admin.users.delete');
        Route::post('/users/{user}/images', 'uploadImages')->name('admin.users.image-uploads');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::post('/products/{product}/images', 'uploadImages')->name('admin.products.image-uploads');
        Route::post('/products', 'store')->name('admin.products.store');
        Route::put('/products/{id}', 'update')->name('admin.products.update');
        Route::delete('/products/{id}', 'destroy')->name('admin.products.delete');
    });
});
