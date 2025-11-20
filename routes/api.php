<?php

use App\Http\Controllers\Central\TenantController;
use App\Http\Controllers\Shared\AuthController;
use App\Http\Controllers\Shared\RoleController;
use App\Http\Controllers\Shared\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::as('api.')
    ->prefix('central')
    ->group(function () {

        Route::get('health', fn() => response()->json('health'));

        Route::group(['prefix' => 'auth'], function () {
            Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
            Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
            Route::post('/send-reset-link', [AuthController::class, 'sendResetLink'])->name('password.send_reset_link');
            Route::get('/validate-token', [AuthController::class, 'validateToken'])->name('password.validate_token');
            Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
            Route::post('/send-verification-email', [AuthController::class, 'sendVerificationEmail'])->name('auth.send_verification');
            Route::post('/verify-email-token', [AuthController::class, 'verifyEmailWithToken'])->name('auth.verify_email_token');
            Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
            Route::post('/verify-email', [AuthController::class, 'sendVerificationEmail'])->name('auth.verify_email');
            Route::get('/bootstrap', [AuthController::class, 'bootstrap'])->name('auth.bootstrap');

            Route::middleware(['auth:sanctum'])->group(function () {
                Route::get('/user', [AuthController::class, 'getUser'])->name('auth.user');
                Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
            });
        });

        Route::prefix('tenants')->group( function (){
            Route::get("/", [TenantController::class, 'findAll'])->name('tenants.findAll');
            Route::get("/{tenant:code}", [TenantController::class, 'show'])->name('tenants.show');
            Route::put("/{tenant}", [TenantController::class, 'update'])->name('tenants.update');
                // ->middleware('auth:sanctum');
            Route::post("/", [TenantController::class, 'store'])->name('tenants.store');
                // ->middleware('auth:sanctum');
        });


        Route::middleware('auth:sanctum')->group(function (): void {
            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'findAll'])->name('user.index');
                Route::post('/', [UserController::class, 'store'])->name('user.store');
                Route::prefix('{user}')->group(function (): void {
                    Route::get('/', [UserController::class, 'show'])->name('user.show');
                    Route::put('/', [UserController::class, 'update'])->name('user.update');
                    Route::delete('/', [UserController::class, 'destroy'])->name('user.destroy');
                });
            });

            Route::prefix('roles')->group(function () {
                Route::get("/", [RoleController::class, 'findAll'])->name('roles.findAll');
                Route::get("/{role}", [RoleController::class, 'findById'])->name('roles.findById');
                Route::post('/', [RoleController::class, 'createRole'])->name('roles.createRole');
                Route::put("/{role}", [RoleController::class, 'updateRole'])->name('roles.updateRole');
            });
        });
    });
