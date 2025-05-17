<?php

use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\FilteredContactController;
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
        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/filtered-contacts', [FilteredContactController::class, 'index'])->name('filtered-contacts.index');
    });
