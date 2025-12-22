<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Buyer;
use App\Models\Admin;

class AuthenticationController extends Controller
{
    /**
     * Register a new account.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|min:4',
                'email'    => 'required|string|email|max:255|unique:buyers,email',
                'password' => 'required|string|min:8',
            ]);

            $buyer = Buyer::create([
                'buyer_name'    => $validated['name'],
                'email'         => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
            ]);

            // Immediately issue a personal access token so the client is authenticated right after registration
            $token = $buyer->createToken('authToken')->plainTextToken;

            return response()->json([
                'response_code' => 201,
                'status'        => 'success',
                'message'       => 'Successfully registered',
                'user_info'     => [
                    'id'    => $buyer->id,
                    'name'  => $buyer->buyer_name,
                    'email' => $buyer->email,
                ],
                'token'       => $token,
                'token_type'  => 'Bearer',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Registration failed',
            ], 500);
        }
    }

    /**
     * Login and return auth token.
     * Automatically detects if email contains "admin" (before @) and checks admin username.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|string',
                'password' => 'required|string',
            ]);

            $email = trim($credentials['email']);
            $password = $credentials['password'];

            // Check if input is exactly "admin" (case-insensitive) or email prefix is "admin"
            $isAdminInput = false;
            $emailPrefix = '';
            
            if (stripos($email, '@') !== false) {
                // Has @ symbol, extract prefix
                $emailParts = explode('@', $email);
                $emailPrefix = strtolower(trim($emailParts[0] ?? ''));
                if ($emailPrefix === 'admin') {
                    $isAdminInput = true;
                }
            } else {
                // No @ symbol, check if it's exactly "admin"
                if (strtolower($email) === 'admin') {
                    $isAdminInput = true;
                }
            }

            // If input is "admin" (with or without @), try admin login
            if ($isAdminInput) {
                // Try to find admin with username "admin"
                $admin = Admin::where('username', 'admin')->first();
                if ($admin && Hash::check($password, (string) $admin->password_hash)) {
                    // Admin login successful
                    $token = $admin->createToken('adminToken')->plainTextToken;

                    return response()->json([
                        'response_code' => 200,
                        'status'        => 'success',
                        'message'       => 'Admin login successful',
                        'user_type'     => 'admin',
                        'admin_info'    => [
                            'id'       => $admin->id,
                            'username' => $admin->username,
                            'role'     => $admin->role,
                        ],
                        'token'      => $token,
                        'token_type' => 'Bearer',
                    ]);
                }
            }

            // If not admin, try buyer login
            $buyer = Buyer::where('email', $email)->first();
            if (!$buyer || !Hash::check($password, (string) $buyer->password_hash)) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Invalid credentials',
                ], 401);
            }

            $token = $buyer->createToken('authToken')->plainTextToken;

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Login successful',
                'user_type'     => 'buyer',
                'user_info'     => [
                    'id'    => $buyer->id,
                    'name'  => $buyer->buyer_name,
                    'email' => $buyer->email,
                ],
                'token'       => $token,
                'token_type'  => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Login failed',
            ], 500);
        }
    }

    /**
     * Admin login and token
     */
    public function adminLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $admin = Admin::where('username', $credentials['username'])->first();
            if (!$admin || !Hash::check($credentials['password'], (string) $admin->password_hash)) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Unauthorized',
                ], 401);
            }

            $token = $admin->createToken('adminToken')->plainTextToken;

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Admin login successful',
                'admin_info'    => [
                    'id'       => $admin->id,
                    'username' => $admin->username,
                    'role'     => $admin->role,
                ],
                'token'      => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Admin Login Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Login failed',
            ], 500);
        }
    }

    /**
     * Get current authenticated user info — protected route.
     */
    public function userInfo(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Unauthorized',
                ], 401);
            }

            // Check if user is a buyer or admin
            if ($user instanceof Buyer) {
                return response()->json([
                    'response_code' => 200,
                    'status'        => 'success',
                    'message'       => 'User info fetched successfully',
                    'user_info'     => [
                        'id'            => $user->id,
                        'buyer_name'    => $user->buyer_name,
                        'name'          => $user->buyer_name,
                        'email'         => $user->email,
                        'buyer_number'  => $user->buyer_number,
                        'address'       => $user->address,
                        'created_at'    => $user->created_at,
                    ],
                ]);
            } elseif ($user instanceof Admin) {
                return response()->json([
                    'response_code' => 200,
                    'status'        => 'success',
                    'message'       => 'User info fetched successfully',
                    'user_info'     => [
                        'id'         => $user->id,
                        'username'   => $user->username,
                        'role'       => $user->role,
                        'created_at' => $user->created_at,
                    ],
                ]);
            }

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Unknown user type',
            ], 500);
        } catch (\Exception $e) {
            Log::error('User Info Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Failed to fetch user info',
            ], 500);
        }
    }

    /**
     * Logout user and revoke tokens — protected route.
     */
    public function logOut(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();

                return response()->json([
                    'response_code' => 200,
                    'status'        => 'success',
                    'message'       => 'Successfully logged out',
                ]);
            }

            return response()->json([
                'response_code' => 401,
                'status'        => 'error',
                'message'       => 'User not authenticated',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'An error occurred during logout',
            ], 500);
        }
    }

    /**
     * Change password using old password
     */
    public function changeWithOldPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'old_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = Buyer::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'response_code' => 404,
                    'status' => 'error',
                    'message' => 'Account not found',
                    'errors' => ['email' => ['Account not found']],
                ], 404);
            }

            if (!Hash::check($validated['old_password'], (string) $user->password_hash)) {
                return response()->json([
                    'response_code' => 401,
                    'status' => 'error',
                    'message' => 'Incorrect password',
                    'errors' => ['old_password' => ['Incorrect password. Try again.']],
                ], 401);
            }

            $user->password_hash = Hash::make($validated['password']);
            $user->save();

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'You have successfully reset your password.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Password Reset Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Password reset failed',
            ], 500);
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyCode(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'code' => 'required|digits:6',
            ]);

            $entry = DB::table('password_reset_codes')
                ->where('email', $validated['email'])
                ->where('code', $validated['code'])
                ->where('expires_at', '>', now())
                ->first();

            if (!$entry) {
                return response()->json([
                    'response_code' => 401,
                    'status' => 'error',
                    'message' => 'Invalid or expired code',
                    'errors' => ['code' => ['Invalid or expired code']],
                ], 401);
            }

            $token = Str::random(32);
            // Store token in database for API (instead of session)
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $validated['email']],
                ['token' => $token, 'created_at' => now(), 'updated_at' => now()]
            );

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Code verified successfully',
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('OTP Verify Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Verification failed',
            ], 500);
        }
    }

    /**
     * Set new password after OTP verification
     */
    public function setNewPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Get token from database
            $tokenEntry = DB::table('password_reset_tokens')
                ->where('token', $validated['token'])
                ->where('created_at', '>', now()->subHours(1)) // Token valid for 1 hour
                ->first();

            if (!$tokenEntry) {
                return response()->json([
                    'response_code' => 401,
                    'status' => 'error',
                    'message' => 'Invalid or expired token',
                ], 401);
            }

            $email = $tokenEntry->email;
            $user = Buyer::where('email', $email)->first() ?? Admin::where('username', $email)->first();

            if (!$user) {
                return response()->json([
                    'response_code' => 404,
                    'status' => 'error',
                    'message' => 'Account not found',
                ], 404);
            }

            $user->password_hash = Hash::make($validated['password']);
            $user->save();

            // Clean up
            DB::table('password_reset_codes')->where('email', $email)->delete();
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'You have successfully reset your password.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Set New Password Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Password reset failed',
            ], 500);
        }
    }
}