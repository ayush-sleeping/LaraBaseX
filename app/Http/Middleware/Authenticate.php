<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }
}

// This middleware handles unauthenticated user redirection.
// If the request does not expect JSON (i.e., not an API call),
// the user will be redirected to the login route.
// You can customize the redirect logic here for different guards or user roles.
