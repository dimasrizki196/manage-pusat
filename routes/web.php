<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\RestockRequestController as AdminRestockRequestController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Barista\RestockRequestController as BaristaRestockRequestController;
use App\Http\Controllers\Barista\TransactionController as BaristaTransactionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Barista\DashboardController as BaristaDashboardController;

Route::middleware('guest')->group(function () {
    Route::get('/', [
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

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
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
                'restore',
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

            Route::resource('items', ItemController::class)
                ->except([
                    'create',
                    'edit',
                ]);

            Route::patch(
                'items/{item}/restore',
                [ItemController::class, 'restore']
            )->name('items.restore');

            Route::delete(
                'items/{item}/force-delete',
                [ItemController::class, 'forceDelete']
            )->name('items.forceDelete');

            Route::resource(
                'transactions',
                AdminTransactionController::class
            )->only([
                'index',
                'store',
                'show',
            ]);

            Route::prefix('restock-requests')
                ->name('restock-requests.')
                ->group(function () {
                    Route::get('/', [AdminRestockRequestController::class, 'index'])
                        ->name('index');

                    Route::get('/{id}', [AdminRestockRequestController::class, 'show'])
                        ->name('show');

                    Route::patch('/{id}/approve', [AdminRestockRequestController::class, 'approve'])
                        ->name('approve');

                    Route::patch('/{id}/reject', [AdminRestockRequestController::class, 'reject'])
                        ->name('reject');

                    Route::delete('/{id}', [AdminRestockRequestController::class, 'destroy'])
                        ->name('destroy');

                    Route::patch('/{id}/restore', [AdminRestockRequestController::class, 'restore'])
                        ->name('restore');

                    Route::delete('/{id}/force', [AdminRestockRequestController::class, 'forceDestroy'])
                        ->name('force-destroy');
                });

            Route::get(
                'activity-logs',
                [ActivityLogController::class, 'index']
            )->name('activity-logs.index');

            Route::get(
                'activity-logs/{id}',
                [ActivityLogController::class, 'show']
            )->name('activity-logs.show');
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

            Route::get('/dashboard', [BaristaDashboardController::class, 'index'])
                ->name('dashboard');

            Route::prefix('transactions')
                ->name('transactions.')
                ->controller(BaristaTransactionController::class)
                ->group(function () {
                    Route::get('/incoming', 'incoming')->name('incoming');
                    Route::post('/incoming', 'storeIncoming')->name('incoming.store');

                    Route::get('/outgoing', 'outgoing')->name('outgoing');
                    Route::post('/outgoing', 'storeOutgoing')->name('outgoing.store');

                    Route::get('/history', 'history')->name('history');
                });

            Route::prefix('restock-requests')
                ->name('restock-requests.')
                ->group(function () {
                    Route::get('/', [BaristaRestockRequestController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [BaristaRestockRequestController::class, 'create'])
                        ->name('create');

                    Route::post('/', [BaristaRestockRequestController::class, 'store'])
                        ->name('store');

                    Route::get('/{id}', [BaristaRestockRequestController::class, 'show'])
                        ->name('show');

                    Route::get('/{id}/edit', [BaristaRestockRequestController::class, 'edit'])
                        ->name('edit');

                    Route::put('/{id}', [BaristaRestockRequestController::class, 'update'])
                        ->name('update');

                    Route::delete('/{id}', [BaristaRestockRequestController::class, 'destroy'])
                        ->name('destroy');
                });
        });
});
