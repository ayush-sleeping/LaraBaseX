<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\EnquiryMail;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class PrimaryController extends Controller
{
    // Show the home page
    public function home(): Response
    {
        return Inertia::render('frontend/home/index');
    }

    // show the services page
    public function services(): Response
    {
        return Inertia::render('frontend/services/index');
    }

    // show the blogs page
    public function blogs(): Response
    {
        $blogs = [
            [
                'title' => 'Laravel Official Documentation',
                'excerpt' => 'The comprehensive guide to Laravel. Learn everything from basics to advanced topics with clear examples and best practices.',
                'image' => 'https://laravel.com/img/logotype.min.svg',
                'link' => 'https://laravel.com/docs/12.x',
            ],
            [
                'title' => 'Laracasts - Laravel Video Tutorials',
                'excerpt' => 'Premium video tutorials for Laravel developers. Master Laravel, PHP, and modern web development with Jeffrey Way.',
                'image' => 'https://assets.laracasts.com/images/secondary-logo.svg',
                'link' => 'https://laracasts.com/',
            ],
            [
                'title' => 'Laravel News & Community',
                'excerpt' => 'Stay updated with the latest Laravel news, tutorials, packages, and community insights from Laravel experts.',
                'image' => 'https://picperf.io/https://laravel-news.com/images/logo.svg',
                'link' => 'https://laravel-news.com',
            ],
            [
                'title' => 'React Official Documentation',
                'excerpt' => 'Learn React from the ground up. Interactive tutorials, guides, and API references for building modern user interfaces.',
                'image' => 'https://react.dev/images/home/conf2021/cover.svg',
                'link' => 'https://react.dev',
            ],
            [
                'title' => 'TypeScript Handbook',
                'excerpt' => 'Master TypeScript with the official handbook. Learn static typing, advanced features, and best practices.',
                'image' => 'https://www.typescriptlang.org/images/branding/ts-lettermark-blue.svg',
                'link' => 'https://www.typescriptlang.org/docs',
            ],
            [
                'title' => 'Shadcn/ui Components',
                'excerpt' => 'Beautiful, accessible UI components built with Radix UI and Tailwind CSS. Copy, paste, and customize for your projects.',
                'image' => 'https://ui.shadcn.com/og.jpg',
                'link' => 'https://ui.shadcn.com',
            ],
        ];

        return Inertia::render('frontend/blogs/index', compact('blogs'));
    }

    // show the about page
    public function about(): Response
    {
        return Inertia::render('frontend/about/index');
    }

    // Show the contact page
    public function contact(): Response
    {
        return Inertia::render('frontend/contact/index');
    }

    // Store contact enquiry
    public function ContactEnquiryStore(Request $request): RedirectResponse
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'nullable',
            'subject' => 'required',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $enquiry = Enquiry::create($request->all());
        // Send an email to the person who submitted the form
        Mail::to($enquiry->email)->send(new EnquiryMail([
            'first_name' => $enquiry->first_name,
            'last_name' => $enquiry->last_name,
            'email' => $enquiry->email,
            'subject' => $request->input('subject'),
            'description' => $request->input('message'),
        ]));

        return redirect('/thankyou');
    }

    public function thankYou(): Response
    {
        return Inertia::render('frontend/contact/thankYou');
    }
}
