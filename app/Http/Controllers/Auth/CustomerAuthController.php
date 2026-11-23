<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('frontend.auth.login');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $providerData = $googleUser->user;

            if (! $googleId || ! $email || ! ($providerData['verified_email'] ?? false)) {
                return redirect()->route('login')->with('error', 'Google did not provide a verified email address.');
            }

            $user = User::where('google_id', $googleId)->first()
                ?? User::where('email', $email)->first();

            if ($user && $user->google_id && $user->google_id !== $googleId) {
                return redirect()->route('login')->with('error', 'This email is already linked to a different Google account.');
            }

            if (! $user) {
                $user = new User;
                $user->name = $googleUser->getName() ?: Str::before($email, '@');
                $user->email = $email;
                $user->google_id = $googleId;
                $user->email_verified_at = now();
                $user->password = Str::random(64);
                $user->save();
            } elseif (! $user->google_id) {
                $user->forceFill([
                    'google_id' => $googleId,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();
            }

            Auth::guard('web')->login($user, remember: true);
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        } catch (Throwable $exception) {
            Log::warning('Google sign-in failed: ' . $exception->getMessage());

            return redirect()->route('login')->with('error', 'Google sign-in could not be completed. Please try again.');
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been signed out.');
    }
}
