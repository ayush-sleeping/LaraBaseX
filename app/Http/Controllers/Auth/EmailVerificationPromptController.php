<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * CODE STRUCTURE SUMMARY:
 * Show the email verification prompt page
 */
class EmailVerificationPromptController extends Controller
{
    /* Show the email verification prompt page. */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : Inertia::render('auth/verify-email', ['status' => $request->session()->get('status')]);
    }
}
