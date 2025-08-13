<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="LaraBaseX API Documentation",
 *      description="A comprehensive API documentation for LaraBaseX - Laravel 12 + ReactJS Full Stack Boilerplate",
 *
 *      @OA\Contact(
 *          email="admin@larabasex.com"
 *      ),
 *
 *      @OA\License(
 *          name="MIT",
 *          url="https://opensource.org/licenses/MIT"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="LaraBaseX API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      description="Enter token in format (Bearer <token>)"
 * )
 * @OA\SecurityScheme(
 *      securityScheme="basicAuth",
 *      type="http",
 *      scheme="basic",
 *      description="Basic authentication for public endpoints"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for User Authentication and Registration"
 * )
 * @OA\Tag(
 *     name="User",
 *     description="API Endpoints for User Profile Management"
 * )
 * @OA\Tag(
 *     name="Home",
 *     description="API Endpoints for Home/Public Content"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
