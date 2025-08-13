<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('clients', ClientController::class)
        ->except(['show'])
        ->parameters(['clients' => 'client']);

    Route::delete('clients/bulk', [ClientController::class, 'bulkDestroy'])->name('clients.bulk-destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
