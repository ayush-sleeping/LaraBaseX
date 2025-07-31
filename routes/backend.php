<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ...existing code...

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// ...existing code...
