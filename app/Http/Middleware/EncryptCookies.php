<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * CODE STRUCTURE SUMMARY:
 * Middleware to encrypt cookies except those specified in $except.
 */
class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add cookie names here if you want them to remain unencrypted.
    ];
}
