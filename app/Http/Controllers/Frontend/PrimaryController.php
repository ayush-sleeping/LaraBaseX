<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrimaryController extends Controller
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

    // show the services page
    public function services()
    {
        return Inertia::render('frontend/services/index');
    }

    // show the blogs page
    public function blogs()
    {
        $blogs = [
            [
                'title' => 'Build websites in minutes with shadcn/ui',
                'excerpt' => 'Pellentesque eget quam ligula. Sed felis ante, consequat nec ultrices ut, ornare quis metus...',
                'image' => 'https://framerusercontent.com/images/R8KAWJ8XJ7xyTu7ucAu7MwYY.png?scale-down-to=512',
                'link' => '#',
            ],
            [
                'title' => 'Easily copy code to build your website',
                'excerpt' => 'Pellentesque eget quam ligula. Sed felis ante, consequat nec ultrices ut, ornare quis metus...',
                'image' => 'https://framerusercontent.com/images/lXJpgpSzhcdgjAHyzQ8gL6xZio.png?scale-down-to=512',
                'link' => '#',
            ],
            [
                'title' => 'The future of web design',
                'excerpt' => 'Pellentesque eget quam ligula. Sed felis ante, consequat nec ultrices ut, ornare quis metus...',
                'image' => 'https://framerusercontent.com/images/swGfymsPbpYnmJh0xWYUDsjYEVw.png?scale-down-to=512',
                'link' => '#',
            ],
        ];
        return Inertia::render('frontend/blogs/index', compact('blogs'));
    }

    // show the about page
    public function about()
    {
        return Inertia::render('frontend/about/index');
    }
}
