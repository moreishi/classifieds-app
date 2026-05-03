<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, CreditService $credits): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'ref' => ['nullable', 'string', 'size:8', 'exists:users,referral_code'],
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => CreditService::generateReferralCode(),
            'free_listings_reset_at' => now()->addMonth(),
        ]);

        // Process referral if provided
        if ($request->filled('ref')) {
            $credits->processReferral($user, $request->ref);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
