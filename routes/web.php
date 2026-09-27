<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\CategoryController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthController::class,
        'showLogin',
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login',
    ])->name('login.process');

    Route::get('/forgot-password', [
        AuthController::class,
        'showForgotPassword'
    ])->name('password.request');

    Route::post('/forgot-password', [
        AuthController::class,
        'forgotPassword'
    ])->name('password.whatsapp');
});


Route::middleware('auth')->group(function () {
    Route::get(
        '/profile',
        [ProfileController::class, 'index']
    )->name('profile.index');

    Route::put(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::put(
        '/profile/password',
        [ProfileController::class, 'updatePassword']
    )->name('profile.password.update');

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::view('/dashboard', 'admin.dashboard')
                ->name('dashboard');

            Route::resource(
                'users',
                UserController::class
            )->except([
                'destroy',
            ]);

            Route::patch(
                'users/{user}/restore',
                [UserController::class, 'restore']
            )->name('users.restore');

            Route::delete(
                'users/{user}',
                [UserController::class, 'destroy']
            )->name('users.destroy');

            Route::delete(
                'users/{user}/force-delete',
                [UserController::class, 'forceDestroy']
            )->name('users.forceDestroy');

            Route::resource(
                'units',
                UnitController::class
            )->only([
                'index',
                'store',
                'update',
                'destroy',
            ]);

            Route::resource(
                'categories',
                CategoryController::class
            )->only([
                'index',
                'store',
                'update',
                'destroy',
            ]);
        });


    /*
    |--------------------------------------------------------------------------
    | Barista
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:barista')
        ->prefix('barista')
        ->name('barista.')
        ->group(function () {

            Route::view('/dashboard', 'barista.dashboard')
                ->name('dashboard');
        });
});
