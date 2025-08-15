<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    // Show the home page
    public function home()
    {
        return Inertia::render('frontend/home/index');
    }

    // Show the contact page
    public function contact()
    {
        return Inertia::render('frontend/contact/index');
    }
}
