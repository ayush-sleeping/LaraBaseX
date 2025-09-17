<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/**
 * CODE STRUCTURE SUMMARY:
 * PreventRequestsDuringMaintenance Middleware
 * Middleware to prevent requests during maintenance mode.
 * Add URIs to $except to allow them during maintenance.
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add URIs here to allow them during maintenance, e.g. 'status', 'api/health', etc.
    ];
}
