<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

/**
 * CODE STRUCTURE SUMMARY:
 * Update the authenticated user's password
 */
class PasswordController extends Controller
{
    /**
     * Update the authenticated user's password.
     * Supports both web and API requests.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        // Use a named error bag for better UX on multi-form pages
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Log out other devices for security
        // Log the password change event
        Auth::logoutOtherDevices($validated['password']);
        Log::info('User password updated', ['user_id' => $user->id]);
        // Return a JSON response if requested, otherwise redirect back
        if ($request->expectsJson()) {
            return response()->json(['status' => 'password-updated']);
        }

        return back()->with('status', 'password-updated');
    }
}
