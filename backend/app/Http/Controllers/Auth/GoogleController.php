<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Notifications\PasswordResetCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
	public function redirectLogin()
	{
		session(['oauth_intent' => 'login']);
		// Store frontend flag and redirect URL if provided
		if (request()->query('frontend') === 'true') {
			session([
				'oauth_is_frontend' => true,
				'oauth_frontend_redirect' => request()->query('redirect', 'http://localhost:3000/shop')
			]);
			\Log::info('Google OAuth: Frontend request detected, redirect URL: ' . request()->query('redirect', 'http://localhost:3000/shop'));
		} else {
			// Clear frontend flag for backend requests
			session()->forget(['oauth_is_frontend', 'oauth_frontend_redirect']);
		}
		// Use the exact redirect URI from config to match Google Console
		$redirect = config('services.google.redirect', 'http://127.0.0.1:8001/oauth/google/callback');
		// Log for debugging
		\Log::info('Google OAuth redirect URI: ' . $redirect);
		/** @var \Laravel\Socialite\Two\GoogleProvider $provider */
		$provider = Socialite::driver('google');
		return $provider->redirectUrl($redirect)->redirect();
	}

	public function callbackLogin(Request $request)
	{
		// Check if user cancelled the OAuth flow
		if ($request->has('error')) {
			$error = $request->query('error');
			\Log::info('Google OAuth cancelled or error: ' . $error);
			
			$isFrontendRequest = session('oauth_is_frontend', false);
			session()->forget(['oauth_is_frontend', 'oauth_frontend_redirect']);
			
			if ($isFrontendRequest) {
				$frontendUrl = config('app.frontend_url', 'http://localhost:3000');
				$redirectUrl = session('oauth_frontend_redirect') ?: ($frontendUrl . '/auth/login');
				return redirect($redirectUrl . (str_contains($redirectUrl, '?') ? '&' : '?') . 'error=' . urlencode('Google sign-in was cancelled.'));
			} else {
				return redirect()->route('login')->with('error', 'Google sign-in was cancelled.');
			}
		}

		$redirect = config('services.google.redirect', url('/oauth/google/callback'));
		/** @var \Laravel\Socialite\Two\GoogleProvider $provider */
		$provider = Socialite::driver('google');
		
		try {
			$googleUser = $provider->redirectUrl($redirect)->stateless()->user();
		} catch (\Exception $e) {
			\Log::error('Google OAuth error: ' . $e->getMessage());
			
			$isFrontendRequest = session('oauth_is_frontend', false);
			session()->forget(['oauth_is_frontend', 'oauth_frontend_redirect']);
			
			if ($isFrontendRequest) {
				$frontendUrl = config('app.frontend_url', 'http://localhost:3000');
				$redirectUrl = session('oauth_frontend_redirect') ?: ($frontendUrl . '/auth/login');
				return redirect($redirectUrl . (str_contains($redirectUrl, '?') ? '&' : '?') . 'error=' . urlencode('Failed to authenticate with Google. Please try again.'));
			} else {
				return redirect()->route('login')->with('error', 'Failed to authenticate with Google. Please try again.');
			}
		}
		
		$email = $googleUser->getEmail();

		// Check if this is a frontend request (stored in session from redirectLogin)
		$isFrontendRequest = session('oauth_is_frontend', false);
		\Log::info('Google OAuth callback: isFrontendRequest=' . ($isFrontendRequest ? 'true' : 'false'));

		// Check if buyer account exists - STRICT: Only registered accounts can access
		$buyer = Buyer::where('email', $email)->first();
		if (!$buyer) {
			// Log the attempt for security
			\Log::warning('Google OAuth login attempt with unregistered email: ' . $email);
			
			if ($isFrontendRequest) {
				// Redirect to frontend with error
				$frontendUrl = config('app.frontend_url', 'http://localhost:3000');
				$redirectUrl = session('oauth_frontend_redirect') ?: ($frontendUrl . '/auth/login');
				session()->forget(['oauth_is_frontend', 'oauth_frontend_redirect']);
				return redirect($redirectUrl . (str_contains($redirectUrl, '?') ? '&' : '?') . 'error=' . urlencode('This Google account (' . $email . ') is not registered. Please register first before signing in with Google.') . '&email=' . urlencode($email));
			} else {
				// Redirect to backend login with error
				session()->forget(['oauth_is_frontend', 'oauth_frontend_redirect']);
				return redirect()->route('login')->with('error', 'This Google account (' . $email . ') is not registered. Please register first before signing in with Google.')->withInput(['email' => $email]);
			}
		}

		Auth::guard('buyer')->login($buyer);
		request()->session()->regenerate();

		if ($isFrontendRequest) {
			// Generate Sanctum token for API access (frontend)
			$token = $buyer->createToken('google-auth')->plainTextToken;
			$frontendUrl = config('app.frontend_url', 'http://localhost:3000');
			// Get redirect URL from session
			$redirectUrl = session('oauth_frontend_redirect') ?: ($frontendUrl . '/shop');
			session()->forget(['oauth_is_frontend', 'oauth_frontend_redirect']);
			\Log::info('Google OAuth: Redirecting to frontend: ' . $redirectUrl);
			return redirect($redirectUrl . (str_contains($redirectUrl, '?') ? '&' : '?') . 'token=' . urlencode($token) . '&success=' . urlencode('Logged in with Google as ' . $email));
		} else {
			// Backend redirect (session-based)
			session()->forget(['oauth_is_frontend', 'oauth_frontend_redirect']);
			return redirect()->route('shop')->with('success', 'Logged in with Google as ' . $email);
		}
	}

	public function redirectReset()
	{
		session(['oauth_intent' => 'reset']);
		$resetRedirect = config('services.google.redirect_reset', url('/oauth/google/reset/callback'));
		/** @var \Laravel\Socialite\Two\GoogleProvider $provider */
		$provider = Socialite::driver('google');
		return $provider->redirectUrl($resetRedirect)->redirect();
	}

	public function callbackReset(Request $request)
	{
		// Check if user cancelled the OAuth flow
		if ($request->has('error')) {
			$error = $request->query('error');
			\Log::info('Google OAuth reset cancelled or error: ' . $error);
			return redirect()->route('password.forgot')->with('error', 'Google sign-in was cancelled.');
		}

		$redirect = config('services.google.redirect_reset', url('/oauth/google/reset/callback'));
		/** @var \Laravel\Socialite\Two\GoogleProvider $provider */
		$provider = Socialite::driver('google');
		
		try {
			$googleUser = $provider->redirectUrl($redirect)->stateless()->user();
		} catch (\Exception $e) {
			\Log::error('Google OAuth reset error: ' . $e->getMessage());
			return redirect()->route('password.forgot')->with('error', 'Failed to authenticate with Google. Please try again.');
		}
		
		$email = $googleUser->getEmail();

		// Only allow if buyer exists
		$buyer = Buyer::where('email', $email)->first();
		if (!$buyer) {
			return redirect()->route('password.forgot')->with('error', 'No customer account found for ' . $email . '.');
		}

		$code = (string) random_int(100000, 999999);
		DB::table('password_reset_codes')->where('email', $email)->delete();
		DB::table('password_reset_codes')->insert([
			'email' => $email,
			'code' => $code,
			'expires_at' => now()->addMinutes(10),
			'created_at' => now(),
			'updated_at' => now(),
		]);

		Notification::route('mail', $email)->notify(new PasswordResetCode($code));

		return redirect()->route('password.verify.form', ['email' => $email])->with('success', 'We sent a 6-digit code to ' . $email);
	}
}

