<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * CODE STRUCTURE SUMMARY:
 * Middleware to verify CSRF tokens, with support for excepted URIs
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add URIs here to exclude them from CSRF protection.
    ];
}
