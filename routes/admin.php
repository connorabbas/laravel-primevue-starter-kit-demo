<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('admin/Dashboard');
        })->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
