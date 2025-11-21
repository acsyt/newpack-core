<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route:: as('api.')
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
                Route::get("/", [RoleController::class, 'findAll'])->name('roles.index');
                Route::get("/{role}", [RoleController::class, 'show'])->name('roles.show');
                Route::post('/', [RoleController::class, 'store'])->name('roles.store');
                Route::put("/{role}", [RoleController::class, 'update'])->name('roles.update');
            });
        });
    });
