<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BuyerRegistered;

class BuyerRegisterController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'buyer_name' => ['required','string','min:4','max:255'],
                'email' => ['required','email','max:255','unique:buyers,email'],
                'password' => ['required','string','min:8','confirmed'],
            ], [
                'buyer_name.min' => 'Name must be at least 4 characters.',
                'email.unique' => 'This email is already registered. Please login instead or use a different email.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            $buyer = Buyer::create([
                'buyer_name' => $validated['buyer_name'],
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
            ]);

            Auth::guard('buyer')->login($buyer);

            // Notify the new buyer (database log + mail-ready if configured)
            if (method_exists($buyer, 'notify')) {
                $buyer->notify(new BuyerRegistered());
            }

            return redirect()->route('shop')->with('success', 'Account created successfully! You are now logged in.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Laravel
            // But we can add a general error message for duplicate email
            if ($e->errors() && isset($e->errors()['email'])) {
                $emailError = $e->errors()['email'][0] ?? '';
                if (str_contains($emailError, 'taken') || str_contains($emailError, 'already')) {
                    return back()->with('error', 'This email is already registered. Please login instead or use a different email.')
                        ->withInput($request->except('password', 'password_confirmation'));
                }
            }
            throw $e; // Re-throw to let Laravel handle other validation errors normally
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed. Please try again later.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
}


