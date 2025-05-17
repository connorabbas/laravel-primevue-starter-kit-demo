<?php

use App\Http\Controllers\Examples\DataTable\ContactController as DataTableContactController;
use App\Http\Controllers\Examples\Paginator\ContactController as PaginatorContactController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('examples')->name('examples.')->group(function () {
    Route::get('/data-table/contacts', [DataTableContactController::class, 'index'])
        ->name('data-table.contacts.index');
    Route::get('/paginator/contacts', [PaginatorContactController::class, 'index'])
        ->name('paginator.contacts.index');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/settings.php';
