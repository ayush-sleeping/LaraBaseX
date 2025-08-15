<?php

use App\Models\User;
use App\Services\QueryCacheService;
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


// Include route files
// -------------------------------------------------------------------------------------------------------- ::

require __DIR__.'/api.php';      // API routes
require __DIR__.'/auth.php';        // Authentication routes
require __DIR__.'/backend.php';   // Admin panel routes
require __DIR__.'/channels.php';  // channels routes
require __DIR__.'/console.php';  // console routes
require __DIR__.'/frontend.php';  // Frontend routes
require __DIR__.'/settings.php';    // User settings routes
