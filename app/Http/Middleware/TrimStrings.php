<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * CODE STRUCTURE SUMMARY:
 * TrimStrings Middleware ( Trims whitespace from all request data except sensitive fields )
 * The names of the attributes that should not be trimmed
 */
class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
