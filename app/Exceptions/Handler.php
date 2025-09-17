<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

/**
 * CODE STRUCTURE SUMMARY:
 * Application exception handler.
 * Customize exception logging, reporting, and rendering here.
 * - Use $levels to set custom log levels for specific exceptions.
 * - Use $dontReport to prevent reporting certain exceptions.
 * - Use $dontFlash to prevent sensitive input from being flashed to the session.
 * You can also add custom renderable/reportable logic in register().
 */
class Handler extends ExceptionHandler
{
    /**
     * Custom log levels for exception types.
     * Example:
     *   \App\Exceptions\CustomException::class => \Psr\Log\LogLevel::WARNING,
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        // \App\Exceptions\CustomException::class => \Psr\Log\LogLevel::WARNING,
    ];

    /**
     * Exception types that are not reported.
     * Example:
     *   \App\Exceptions\CustomException::class,
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        // \App\Exceptions\CustomException::class,
    ];

    /**
     * Inputs never flashed to session on validation errors.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /* Register exception handling callbacks. */
    public function register(): void
    {
        // Example: Custom reporting logic
        $this->reportable(function (Throwable $e) {
            // Add custom reporting logic here if needed.
        });

        // Handle 403 Forbidden errors for Inertia requests
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->header('X-Inertia')) {
                return \Inertia\Inertia::render('error', [
                    'status' => 403,
                    'message' => $e->getMessage() ?: 'You don\'t have permission to access this resource.',
                ])->toResponse($request)->setStatusCode(403);
            }
        });

        // Handle general authorization exceptions for Inertia requests
        $this->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->header('X-Inertia')) {
                return \Inertia\Inertia::render('error', [
                    'status' => 403,
                    'message' => $e->getMessage() ?: 'You don\'t have permission to access this resource.',
                ])->toResponse($request)->setStatusCode(403);
            }
        });

        // Example: Custom renderable exception (e.g., for API/SPA JSON responses)
        // $this->renderable(function (\App\Exceptions\CustomException $e, $request) {
        //     return response()->json(['error' => $e->getMessage()], 400);
        // });
    }
}
