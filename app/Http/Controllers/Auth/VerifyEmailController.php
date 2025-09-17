<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * CODE STRUCTURE SUMMARY:
 * Mark the authenticated user's email address as verified
 */
class VerifyEmailController extends Controller
{
    /* Mark the authenticated user's email address as verified. */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }
        // fulfill() marks the email as verified and fires the Verified event
        $request->fulfill();

        return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }
}
