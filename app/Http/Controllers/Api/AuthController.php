<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /* Get the current app version and store URL. */
    public function getAppVersion()
    {
        $data = [
            'app_version' => env('APP_VERSION', '1.0.0+0'),
            'url' => env('PLAY_STORE_URL', 'https://play.google.com/store/apps/details?id=com.example.app'),
        ];
        return response()->json($data, 200);
    }


    /* Register a new user and send OTP. */
    public function register(Request $request)
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

        $otp = rand(1000, 9999);
        $user = new User;
        $user->fill($request->all());
        $user->password = Hash::make($otp);
        $user->save();

        // TODO: Integrate actual OTP sending logic here
        // Log::info('OTP sent to user', ['mobile' => $user->mobile, 'otp' => $otp]);

        return response()->json(['message' => 'OTP sent successfully'], 201);
    }


    /* Login user and send OTP. */
    public function login(Request $request)
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
            $otp = rand(1000, 9999);
            $user->password = Hash::make($otp);
            $user->device_id = $request->device_id;
            $user->save();

            // TODO: Integrate actual OTP sending logic here
            // Log::info('OTP sent to user', ['mobile' => $user->mobile, 'otp' => $otp]);

            return response()->json(['message' => 'OTP sent successfully'], 200);
        } else {
            return response()->json(['errors' => ['mobile' => ['Mobile number does not exist']]], 401);
        }
    }


    /*  Verify OTP and issue access token. */
    public function verifyOTP(Request $request)
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
            $token = $tokenResult->token;
            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1);
            }
            $token->save();
            return response()->json([
                'message' => 'User logged in successfully',
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
            ], 200);
        } else {
            return response()->json(['message' => 'OTP does not match, please enter correct OTP'], 401);
        }
    }


    /* Resend OTP to user. */
    public function resendOTP(Request $request)
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
            $otp = rand(1000, 9999);
            $user->password = Hash::make($otp);
            $user->save();

            // TODO: Integrate actual OTP sending logic here
            // Log::info('OTP resent to user', ['mobile' => $user->mobile, 'otp' => $otp]);

            return response()->json(['message' => 'OTP sent successfully'], 200);
        } else {
            return response()->json(['message' => 'Mobile number does not exist'], 401);
        }
    }


    /* Logout the authenticated user (revoke token). */
    public function logout(Request $request)
    {
        $user = $request->user('api');
        if ($user && method_exists($user, 'tokens')) {
            // Revoke all tokens for the user (Passport)
            $user->tokens->each(function ($token) {
                $token->revoke();
            });
        }
        return response()->json(['message' => 'User logged out successfully'], 200);
    }


    /* Get the authenticated user's details. */
    public function getUser(Request $request)
    {
        $user = $request->user('api');
        return response()->json($user, 200);
    }


    /* Update the authenticated user's details. */
    public function updateUser(Request $request)
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
        return response()->json(['message' => 'User updated successfully'], 200);
    }


    /* Update the authenticated user's photo. */
    public function updateUserPhoto(Request $request)
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
            return response()->json(['message' => 'Photo updated successfully'], 200);
        }
        return response()->json(['message' => 'No photo uploaded'], 400);
    }
}
