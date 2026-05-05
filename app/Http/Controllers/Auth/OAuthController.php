<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        Log::debug('OAuth: redirecting to Google');
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        Log::debug('OAuth: callback received', [
            'code' => request()->has('code'),
            'error' => request()->has('error'),
            'state' => request()->has('state'),
            'full_url' => request()->fullUrl(),
        ]);

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::debug('OAuth: Google user retrieved', [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
            ]);
        } catch (\Exception $e) {
            Log::error('OAuth: Google sign-in exception', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('status', 'Google sign-in failed. Please try again.');
        }

        try {
            $user = $this->findOrCreateOAuthUser($googleUser, 'google');

            Auth::login($user, true);

            Log::debug('OAuth: login successful', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            Log::error('OAuth: user creation/login exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('status', 'Account creation failed. Please try again or use email sign-up.');
        }
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

        // Existing user — never sync avatar or display_name again.
        // Those are only set once during registration.
        if ($user) {
            // Google-verified email → ensure verified_at is set
            // (handles legacy accounts that got linked before we set this)
            if (! $user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
                Log::debug('OAuth: fixed missing email_verified_at for existing user', ['user_id' => $user->id]);
            }
            Log::debug('OAuth: existing user by oauth_id', ['user_id' => $user->id]);
            return $user;
        }

        // Next, try to find by email (existing account → link it)
        $user = User::where('email', $oauthUser->getEmail())->first();

        if ($user) {
            $user->update([
                'oauth_id' => $oauthUser->getId(),
                'oauth_provider' => $provider,
                'avatar_url' => $user->avatar_url ?? $oauthUser->getAvatar(),
                'display_name' => $user->display_name ?? $oauthUser->getName(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
            Log::debug('OAuth: existing user linked by email', ['user_id' => $user->id]);

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

        $user = User::create([
            'oauth_id' => $oauthUser->getId(),
            'oauth_provider' => $provider,
            'name' => $name,
            'display_name' => $name,
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

        Log::debug('OAuth: new user created', ['user_id' => $user->id, 'email' => $user->email]);

        return $user;
    }
}
