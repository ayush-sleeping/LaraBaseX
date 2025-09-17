<?php

use App\Http\Controllers\Frontend\PrimaryController;
use Illuminate\Support\Facades\Route;

// Home page route
// -------------------------------------------------------------------------------------------------------- ::
Route::get('/', [PrimaryController::class, 'home'])->name('home');
Route::get('/services', [PrimaryController::class, 'services'])->name('services');
Route::get('/blogs', [PrimaryController::class, 'blogs'])->name('blogs');
Route::get('/about', [PrimaryController::class, 'about'])->name('about');
Route::get('/contact', [PrimaryController::class, 'contact'])->name('contact');
Route::post('/contact/store', [PrimaryController::class, 'ContactEnquiryStore'])->name('contact.store');
Route::get('/thankyou', [PrimaryController::class, 'thankYou'])->name('contact.thankyou');
