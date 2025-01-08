<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;


class SocialController extends AppBaseController
{
    /**
     * Redirect the user to Google’s OAuth page.
     */
    public function redirect()
    {
        $socialite = Socialite::driver('google')->stateless()->redirect();
        // $socialite = Socialite::driver('google')->redirect();
        return $socialite;
    }

    /**
     * Handle the callback from Google.
     */
    public function callback()
    {  
        try {
            // Get the user information from Google
            $user = Socialite::driver('google')->stateless()->user();
        } catch (Throwable $e) {
            return redirect('/')->with('error', 'Google authentication failed.');
        } 

        // Check if the user already exists in the database
        $existingUser = User::where('email', $user->email)->first();
        // dd( $user, $existingUser);

        if ($existingUser) {
            // Log the user in if they already exist
            Auth::login($existingUser);
        } else {
            // Otherwise, create a new user and log them in
            $newUser = User::updateOrCreate([
                'email' => $user->email
            ], [
                'name' => $user->name,
                'password' => bcrypt(Str::random(16)), // Set a random password
                'email_verified_at' => now()
            ]);
            Auth::login($newUser);
        }

        // Redirect the user to the dashboard or any other secure page
        // return redirect('/dashboard');
    }
}