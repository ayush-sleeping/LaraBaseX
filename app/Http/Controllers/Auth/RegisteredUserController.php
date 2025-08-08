<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /* Show the registration page. */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /*
     * Handle an incoming registration request.
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'INACTIVE', // New users start as INACTIVE until admin approves
        ]);
        
        // TODO: Assign default "User" role once roles are properly seeded
        // $user->assignRole('User');
        
        event(new Registered($user));

        // Don't auto-login INACTIVE users - redirect to login with message
        return redirect()->route('login')->with('status', 'Registration successful! Your account is pending approval by an administrator.');
    }
}
