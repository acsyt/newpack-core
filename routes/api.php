<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::as('api.')
->group(function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
        Route::post('/send-reset-link', [AuthController::class, 'forgotPassword'])->name('password.send_reset_link');

        Route::get('/validate-token', [AuthController::class, 'validateToken'])->name('password.validate_token');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

        Route::post('/send-verification-email', [AuthController::class, 'sendVerificationEmail'])->name('auth.send_verification');

        Route::post('/verify-email-token', [AuthController::class, 'verifyEmailWithToken'])->name('auth.verify_email_token');
        Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/user', [AuthController::class, 'getUser'])->name('auth.user');
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        });
    });

    // Route::middleware('auth:sanctum')->group(function (): void {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'findAllUsers'])->name('user.findAll');
            Route::post('/', [UserController::class, 'createUser'])->name('user.create');
            Route::prefix('{user}')->group(function (): void {
                Route::get('/', [UserController::class, 'findOneUser'])->name('user.findOne');
                Route::put('/', [UserController::class, 'updateUser'])->name('user.update');
                Route::delete('/', [UserController::class, 'deleteUser'])->name('user.delete');
            });
        });

        Route::prefix('roles')->group(function () {
            Route::get("/", [RoleController::class, 'findAllRoles'])->name('roles.findAll');
            Route::get("/{role}", [RoleController::class, 'findOneRole'])->name('roles.findOne');
            Route::post('/', [RoleController::class, 'createRole'])->name('roles.create');
            Route::put("/{role}", [RoleController::class, 'updateRole'])->name('roles.update');
        });

        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'findAllCustomers'])->name('customers.findAll');
            Route::post('/', [CustomerController::class, 'createCustomer'])->name('customers.create');
            Route::prefix('{customer}')->group(function () {
                Route::get('/', [CustomerController::class, 'findOneCustomer'])->name('customers.findOne');
                Route::put('/', [CustomerController::class, 'updateCustomer'])->name('customers.update');
                Route::delete('/', [CustomerController::class, 'deleteCustomer'])->name('customers.delete');
            });
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'findAllProducts'])->name('products.findAll');
            Route::post('/', [ProductController::class, 'createProduct'])->name('products.create');
            Route::prefix('{product}')->group(function () {
                Route::get('/', [ProductController::class, 'findOneProduct'])->name('products.findOne');
                Route::put('/', [ProductController::class, 'updateProduct'])->name('products.update');
                Route::delete('/', [ProductController::class, 'deleteProduct'])->name('products.delete');
            });
        });

        Route::prefix('suppliers')->group(function () {
            Route::get('/', [SupplierController::class, 'findAllSuppliers'])->name('suppliers.findAll');
            Route::post('/', [SupplierController::class, 'createSupplier'])->name('suppliers.create');
            Route::prefix('{supplier}')->group(function () {
                Route::get('/', [SupplierController::class, 'findOneSupplier'])->name('suppliers.findOne');
                Route::put('/', [SupplierController::class, 'updateSupplier'])->name('suppliers.update');
                Route::delete('/', [SupplierController::class, 'deleteSupplier'])->name('suppliers.delete');
            });
        });

    // });
});
