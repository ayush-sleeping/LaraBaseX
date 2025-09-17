<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CODE STRUCTURE SUMMARY:
 * Home-related API endpoints.
 * Get home page sliders
 * Get home page content (Not Impmented)
 * Get featured products (Not Impmented)
 * Get testimonials (Not Impmented)
 * Get FAQs (Not Impmented)
 */
class HomeController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/sliders",
     *     operationId="getSliders",
     *     tags={"Home"},
     *     summary="Get home page sliders",
     *     description="Retrieve all active sliders for the home page",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Sliders retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
     *                 @OA\Property(property="id", type="integer", example=1, description="Slider ID"),
     *                 @OA\Property(property="title", type="string", example="Welcome to LaraBaseX", description="Slider title"),
     *                 @OA\Property(property="description", type="string", example="Best platform for your needs", description="Slider description"),
     *                 @OA\Property(property="image", type="string", example="sliders/slider1.jpg", description="Slider image path"),
     *                 @OA\Property(property="link", type="string", nullable=true, example="https://example.com", description="Optional link URL"),
     *                 @OA\Property(property="is_active", type="boolean", example=true, description="Whether slider is active"),
     *                 @OA\Property(property="order", type="integer", example=1, description="Display order"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */

    // Get home page sliders
    public function getSliders(Request $request): JsonResponse
    {
        // For example: getLanguages, getQuotes etc.
        // Use request validation, resource collections, and clear comments for each method.

        // Placeholder implementation - replace with actual slider logic
        $sliders = [
            [
                'id' => 1,
                'title' => 'Welcome to LaraBaseX',
                'description' => 'Best platform for your needs',
                'image' => 'sliders/slider1.jpg',
                'link' => null,
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        return response()->json($sliders, 200);
    }
}
