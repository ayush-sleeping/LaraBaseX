<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Home page route
// -------------------------------------------------------------------------------------------------------- ::
Route::get('/', function () { return Inertia::render('welcome'); })->name('home');

// Include route files
// -------------------------------------------------------------------------------------------------------- ::
require __DIR__.'/settings.php';    // User settings routes
require __DIR__.'/auth.php';        // Authentication routes
require __DIR__ . '/backend.php';   // Admin panel routes
require __DIR__ . '/frontend.php';  // Frontend routes
