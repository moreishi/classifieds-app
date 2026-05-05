<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('status', 'Google sign-in failed. Please try again.');
        }

        $user = $this->findOrCreateOAuthUser($googleUser, 'google');

        Auth::login($user, true);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Find existing user by OAuth ID/email, or create a new one.
     */
    private function findOrCreateOAuthUser($oauthUser, string $provider): User
    {
        // First, try to find by OAuth ID
        $user = User::where('oauth_id', $oauthUser->getId())
            ->where('oauth_provider', $provider)
            ->first();

        if ($user) {
            return $user;
        }

        // Next, try to find by email (existing account → link it)
        $user = User::where('email', $oauthUser->getEmail())->first();

        if ($user) {
            $user->update([
                'oauth_id' => $oauthUser->getId(),
                'oauth_provider' => $provider,
                'avatar_url' => $user->avatar_url ?? $oauthUser->getAvatar(),
            ]);

            return $user;
        }

        // New user — create account
        $name = $oauthUser->getName();
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0] ?? $name;
        $lastName = $nameParts[1] ?? '';

        $baseUsername = Str::slug($firstName . '-' . $lastName) ?: 'user';
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return User::create([
            'oauth_id' => $oauthUser->getId(),
            'oauth_provider' => $provider,
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $oauthUser->getEmail(),
            'username' => $username,
            'avatar_url' => $oauthUser->getAvatar(),
            'email_verified_at' => now(),
            'reputation_points' => 0,
            'reputation_tier' => 'new',
            'buyer_points' => 0,
            'free_listings_used' => 0,
            'credit_balance' => 0,
        ]);
    }
}
