<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Buyer;
use App\Notifications\PasswordResetCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
	public function showOptions(): View
	{
		return view('auth.password.forgot');
	}

	public function changeWithOldPassword(Request $request)
	{
		$data = $request->validate([
			'email' => ['required', 'email'],
			'old_password' => ['required', 'string'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
		]);

		$user = Buyer::where('email', $data['email'])->first();

		if (!$user) {
			return back()->withErrors(['email' => 'Account not found'])->withInput();
		}

		if (!Hash::check($data['old_password'], $user->password_hash)) {
			return back()->withErrors(['old_password' => 'Incorrect password. Try again.'])->withInput();
		}

		$user->password_hash = Hash::make($data['password']);
		$user->save();

		return redirect()->route('login')->with('success', 'You have successfully reset your password.');
	}

	public function sendOtp(Request $request)
	{
		$validated = $request->validate([
			'email' => ['required', 'email'],
		]);

		$email = $validated['email'];
		$user = Buyer::where('email', $email)->first() ?? Admin::where('username', $email)->first() ?? Buyer::where('email', $email)->first();
		if (!$user) {
			return back()->withErrors(['email' => 'No account found for that email'])->withInput();
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

		return redirect()->route('password.verify.form', ['email' => $email])->with('success', 'We sent a 6-digit code to your email.');
	}

	public function showVerifyForm(Request $request): View
	{
		$email = $request->query('email');
		return view('auth.password.verify', compact('email'));
	}

	public function verifyCode(Request $request)
	{
		$data = $request->validate([
			'email' => ['required', 'email'],
			'code' => ['required', 'digits:6'],
		]);

		$entry = DB::table('password_reset_codes')
			->where('email', $data['email'])
			->where('code', $data['code'])
			->where('expires_at', '>', now())
			->first();

		if (!$entry) {
			return back()->withErrors(['code' => 'Invalid or expired code'])->withInput();
		}

		$token = Str::random(32);
		session(['password_reset_email' => $data['email'], 'password_reset_token' => $token]);

		return redirect()->route('password.new.form', ['token' => $token]);
	}

	public function showNewPasswordForm(Request $request): View
	{
		$token = $request->query('token');
		return view('auth.password.new', compact('token'));
	}

	public function setNewPassword(Request $request)
	{
		$data = $request->validate([
			'token' => ['required', 'string'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
		]);

		$sessionToken = session('password_reset_token');
		$email = session('password_reset_email');
		if (!$sessionToken || $sessionToken !== $data['token'] || !$email) {
			return redirect()->route('password.forgot')->with('error', 'Session expired. Start again.');
		}

		$user = Buyer::where('email', $email)->first() ?? Admin::where('username', $email)->first();
		if (!$user) {
			return redirect()->route('password.forgot')->with('error', 'Account not found.');
		}

		$user->password_hash = Hash::make($data['password']);
		$user->save();

		DB::table('password_reset_codes')->where('email', $email)->delete();
		session()->forget(['password_reset_email', 'password_reset_token']);

		return redirect()->route('login')->with('success', 'You have successfully reset your password.');
	}
}
