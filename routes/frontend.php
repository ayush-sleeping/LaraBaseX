<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\PrimaryController;


// Home page route
// -------------------------------------------------------------------------------------------------------- ::
Route::get('/', [PrimaryController::class, 'home'])->name('home');
Route::get('/contact', [PrimaryController::class, 'contact'])->name('contact');
Route::get('/services', [PrimaryController::class, 'services'])->name('services');
Route::get('/blogs', [PrimaryController::class, 'blogs'])->name('blogs');
Route::get('/about', [PrimaryController::class, 'about'])->name('about');
