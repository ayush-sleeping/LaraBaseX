<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CODE STRUCTURE SUMMARY:
 * Authentication-related API endpoints.
 * Get application version and store URL
 * Register a new user
 * Login user
 * Verify OTP for user login
 * Resend OTP to user's mobile
 * Logout user
 * Get authenticated user details
 * Update user details
 * Update user profile photo
 */
class AuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/app-version",
     *     operationId="getAppVersion",
     *     tags={"Authentication"},
     *     summary="Get application version and store URL",
     *     description="Returns the current application version and app store URL",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="app_version", type="string", example="1.0.0+0", description="Current app version"),
     *             @OA\Property(property="url", type="string", example="https://play.google.com/store/apps/details?id=com.example.app", description="App store URL")
     *         )
     *     )
     * )
     */

    // Get application version and store URL
    public function getAppVersion(): JsonResponse
    {
        $data = [
            'app_version' => config('app.version', '1.0.0+0'),
            'url' => config('app.play_store_url', 'https://play.google.com/store/apps/details?id=com.example.app'),
        ];

        return response()->json($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Register a new user and send OTP for verification",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"first_name","email","mobile","device_id"},
     *
     *             @OA\Property(property="first_name", type="string", maxLength=191, example="John", description="User's first name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email address"),
     *             @OA\Property(property="mobile", type="string", pattern="^[0-9]{10}$", example="9876543210", description="10-digit mobile number"),
     *             @OA\Property(property="device_id", type="string", maxLength=191, example="abc123device", description="Unique device identifier")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully, OTP sent",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="OTP sent successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="first_name", type="array", @OA\Items(type="string", example="Please enter name")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="Please enter valid email")),
     *                 @OA\Property(property="mobile", type="array", @OA\Items(type="string", example="Mobile number already exists"))
     *             )
     *         )
     *     )
     * )
     */

    // Register a new user
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:191',
            'email' => 'required|email',
            'mobile' => 'required|numeric|unique:users,mobile|digits:10',
            'device_id' => 'required|max:191',
        ], [
            'first_name.required' => 'Please enter name',
            'first_name.max' => 'Name should not be more than 191 characters',
            'email.required' => 'Please enter email',
            'email.email' => 'Please enter valid email',
            'mobile.required' => 'Please enter mobile',
            'mobile.numeric' => 'Please enter valid mobile',
            'mobile.unique' => 'Mobile number already exists',
            'mobile.digits' => 'Please enter 10 digits mobile',
            'device_id.required' => 'device_id is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $otp = (string) rand(1000, 9999);
        $user = new User;
        $user->fill($request->all());
        $user->password = Hash::make($otp);
        $user->save();

        // TODO: Integrate actual OTP sending logic here
        // Log::info('OTP sent to user', ['mobile' => $user->mobile, 'otp' => $otp]);

        return api_success_message('otp_sent');
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Login user with mobile number and send OTP",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"mobile","device_id"},
     *
     *             @OA\Property(property="mobile", type="string", pattern="^[0-9]{10}$", example="9876543210", description="10-digit mobile number"),
     *             @OA\Property(property="device_id", type="string", maxLength=191, example="abc123device", description="Unique device identifier")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="OTP sent successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="mobile", type="array", @OA\Items(type="string", example="Please enter valid mobile"))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Mobile number not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="mobile", type="array", @OA\Items(type="string", example="Mobile number does not exist"))
     *             )
     *         )
     *     )
     * )
     */

    // Login user
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits:10',
            'device_id' => 'required|max:191',
        ], [
            'mobile.required' => 'Please enter mobile',
            'mobile.numeric' => 'Please enter valid mobile',
            'mobile.digits' => 'Please enter 10 digits mobile',
            'device_id.required' => 'device_id is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user = User::where('mobile', $request->mobile)->first();
        if ($user) {
            $otp = (string) rand(1000, 9999);
            $user->password = Hash::make($otp);
            $user->device_id = $request->device_id;
            $user->save();

            // TODO: Integrate actual OTP sending logic here
            // Log::info('OTP sent to user', ['mobile' => $user->mobile, 'otp' => $otp]);

            return api_success_message('otp_sent');
        } else {
            return api_error_message('unauthorized', 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/verify-otp",
     *     operationId="verifyOTP",
     *     tags={"Authentication"},
     *     summary="Verify OTP and get access token",
     *     description="Verify the OTP sent to user's mobile and issue access token",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"mobile","otp"},
     *
     *             @OA\Property(property="mobile", type="string", pattern="^[0-9]{10}$", example="9876543210", description="10-digit mobile number"),
     *             @OA\Property(property="otp", type="string", pattern="^[0-9]{4}$", example="1234", description="4-digit OTP code"),
     *             @OA\Property(property="remember_me", type="boolean", example=true, description="Remember login for extended period")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...", description="JWT access token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer", description="Token type"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", example="2024-01-15 10:30:00", description="Token expiration date")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="mobile", type="array", @OA\Items(type="string", example="Please enter valid mobile")),
     *                 @OA\Property(property="otp", type="array", @OA\Items(type="string", example="Please enter 4 digits for OTP"))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Invalid OTP",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="OTP does not match, please enter correct OTP")
     *         )
     *     )
     * )
     */

    // Verify OTP for user login
    public function verifyOTP(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits:10',
            'otp' => 'required|digits:4',
        ], [
            'mobile.required' => 'Please enter mobile',
            'mobile.numeric' => 'Please enter valid mobile',
            'mobile.digits' => 'Please enter 10 digits mobile',
            'otp.required' => 'Please enter OTP',
            'otp.digits' => 'Please enter 4 digits for OTP',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $credentials = ['mobile' => $request->mobile, 'password' => $request->otp];
        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $accessToken = $tokenResult->accessToken ?? null;
            /** @phpstan-ignore-next-line */
            $passportToken = $tokenResult->token;
            if ($request->remember_me) {
                $passportToken->expires_at = Carbon::now()->addWeeks(1);
                $passportToken->save();
            }

            return response()->json([
                'message' => api_message('otp_verified'),
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($passportToken->expires_at)->toDateTimeString(),
            ], 200);
        } else {
            return api_error_message('otp_invalid', 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/resend-otp",
     *     operationId="resendOTP",
     *     tags={"Authentication"},
     *     summary="Resend OTP",
     *     description="Resend OTP to user's mobile number",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"mobile"},
     *
     *             @OA\Property(property="mobile", type="string", pattern="^[0-9]{10}$", example="9876543210", description="10-digit mobile number")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OTP resent successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="OTP sent successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="mobile", type="array", @OA\Items(type="string", example="Please enter valid mobile"))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Mobile number not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Mobile number does not exist")
     *         )
     *     )
     * )
     */

    // Resend OTP to user's mobile
    public function resendOTP(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits:10',
        ], [
            'mobile.required' => 'Please enter mobile',
            'mobile.numeric' => 'Please enter valid mobile',
            'mobile.digits' => 'Please enter 10 digits mobile',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user = User::where('mobile', $request->mobile)->first();
        if ($user) {
            $otp = (string) rand(1000, 9999);
            $user->password = Hash::make($otp);
            $user->save();

            // TODO: Integrate actual OTP sending logic here
            // Log::info('OTP resent to user', ['mobile' => $user->mobile, 'otp' => $otp]);

            return api_success_message('otp_sent');
        } else {
            return api_error_message('not_found', 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     description="Logout authenticated user and revoke access token",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User logged out successfully")
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

    // Logout user
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user('api');
        if ($user) {
            $tokens = $user->tokens;
            $tokens->each(function ($token) {
                $token->revoke();
            });
        }

        return api_success_message('logout_successful');
    }

    /**
     * @OA\Post(
     *     path="/api/user",
     *     operationId="getUser",
     *     tags={"User"},
     *     summary="Get authenticated user details",
     *     description="Returns authenticated user's profile information",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id", type="integer", example=1, description="User ID"),
     *             @OA\Property(property="first_name", type="string", example="John", description="User's first name"),
     *             @OA\Property(property="last_name", type="string", example="Doe", description="User's last name"),
     *             @OA\Property(property="email", type="string", example="john@example.com", description="User's email"),
     *             @OA\Property(property="mobile", type="string", example="9876543210", description="User's mobile number"),
     *             @OA\Property(property="business_name", type="string", example="John's Business", description="Business name"),
     *             @OA\Property(property="state", type="string", example="California", description="User's state"),
     *             @OA\Property(property="city", type="string", example="Los Angeles", description="User's city"),
     *             @OA\Property(property="photo", type="string", nullable=true, example="user-photos/photo.jpg", description="Profile photo path"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T10:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T10:00:00.000000Z")
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

    // Get authenticated user details
    public function getUser(Request $request): JsonResponse
    {
        $user = $request->user('api');

        return response()->json($user, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/user/update",
     *     operationId="updateUser",
     *     tags={"User"},
     *     summary="Update authenticated user details",
     *     description="Update authenticated user's profile information",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"business_name","first_name","last_name","state","city"},
     *
     *             @OA\Property(property="business_name", type="string", maxLength=50, example="John's Updated Business", description="Business name"),
     *             @OA\Property(property="first_name", type="string", maxLength=50, example="John", description="User's first name"),
     *             @OA\Property(property="last_name", type="string", maxLength=50, example="Doe", description="User's last name"),
     *             @OA\Property(property="state", type="string", example="California", description="User's state"),
     *             @OA\Property(property="city", type="string", example="Los Angeles", description="User's city")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User updated successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="business_name", type="array", @OA\Items(type="string", example="Please enter business name")),
     *                 @OA\Property(property="first_name", type="array", @OA\Items(type="string", example="Please enter first name"))
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

    // Update user details
    public function updateUser(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|max:50',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'state' => 'required',
            'city' => 'required',
        ], [
            'business_name.required' => 'Please enter business name',
            'first_name.required' => 'Please enter first name',
            'last_name.required' => 'Please enter last name',
            'state.required' => 'Please select state',
            'city.required' => 'Please select city',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user->fill($request->all());
        $user->save();

        return api_success_message('updated', $user);
    }

    /**
     * @OA\Post(
     *     path="/api/user/update-photo",
     *     operationId="updateUserPhoto",
     *     tags={"User"},
     *     summary="Update user profile photo",
     *     description="Upload and update authenticated user's profile photo",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"photo"},
     *
     *                 @OA\Property(property="photo", type="string", format="binary", description="Profile photo file (JPEG, JPG, PNG)")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Photo updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Photo updated successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or no photo uploaded",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No photo uploaded")
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

    // Update user profile photo
    public function updateUserPhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|mimes:jpeg,jpg,png',
        ], [
            'photo.required' => 'Please select photo',
        ]);

        $user = $request->user('api');
        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($user->photo);
            $user->photo = Storage::disk('public')->put('user-photos', $request->photo);
            $user->save();

            return api_success_message('file.uploaded');
        }

        return api_error_message('validation_failed', 400);
    }
}
