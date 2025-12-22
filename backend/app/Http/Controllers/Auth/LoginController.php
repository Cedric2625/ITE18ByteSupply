<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Buyer;
use App\Models\LoginActivity;

class LoginController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
        ]);

        // Try admin first (username), then buyer (email)
        if (Auth::guard('admin')->attempt([
            'username' => $validated['username'],
            'password' => $validated['password'],
        ])) {
            // Ensure buyer session is not simultaneously active
            if (Auth::guard('buyer')->check()) {
                Auth::guard('buyer')->logout();
            }
            $request->session()->regenerate();
            LoginActivity::create([
                'user_type' => 'admin',
                'user_id' => Auth::guard('admin')->id(),
                'event' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, admin!');
        }

        if (Auth::guard('buyer')->attempt([
            'email' => $validated['username'],
            'password' => $validated['password'],
        ])) {
            // Ensure admin session is not simultaneously active
            if (Auth::guard('admin')->check()) {
                Auth::guard('admin')->logout();
            }
            $request->session()->regenerate();
            LoginActivity::create([
                'user_type' => 'buyer',
                'user_id' => Auth::guard('buyer')->id(),
                'event' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
            return redirect()->intended(route('shop'))->with('success', 'Welcome back!');
        }

        return back()->withErrors(['username' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            LoginActivity::create([
                'user_type' => 'admin',
                'user_id' => Auth::guard('admin')->id(),
                'event' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
            Auth::guard('admin')->logout();
        }
        if (Auth::guard('buyer')->check()) {
            LoginActivity::create([
                'user_type' => 'buyer',
                'user_id' => Auth::guard('buyer')->id(),
                'event' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
            Auth::guard('buyer')->logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out.');
    }
}


