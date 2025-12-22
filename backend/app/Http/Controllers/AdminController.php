<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display a listing of the admins.
     */
    public function index(Request $request): View|JsonResponse
    {
        $admins = Admin::withCount('orders')->get();
        
        if ($request->expectsJson()) {
            return response()->json($admins);
        }
        
        return view('admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create(): View
    {
        return view('admins.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(StoreAdminRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password_hash'] = Hash::make($validated['password']);
        unset($validated['password']);

        $admin = Admin::create($validated);

        if ($request->expectsJson()) {
            return response()->json($admin, 201);
        }

        return response()->json([
            'message' => 'Admin created successfully',
            'redirect' => route('admins.show', $admin)
        ], 201);
    }

    /**
     * Display the specified admin.
     */
    public function show(Request $request, Admin $admin): View|JsonResponse
    {
        $admin->loadCount('orders');

        if ($request->expectsJson()) {
            return response()->json($admin);
        }

        return view('admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(Admin $admin): View
    {
        return view('admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $admin): JsonResponse
    {
        $validated = $request->validated();
        
        if (isset($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
            unset($validated['password']);
        }

        $admin->update($validated);

        if ($request->expectsJson()) {
            return response()->json($admin);
        }

        return response()->json([
            'message' => 'Admin updated successfully',
            'redirect' => route('admins.show', $admin)
        ]);
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(Request $request, Admin $admin): JsonResponse
    {
        // Check if admin has any orders
        if ($admin->orders()->exists()) {
            return response()->json([
                'message' => 'Cannot delete admin with associated orders'
            ], 422);
        }

        $admin->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Admin deleted successfully']);
        }

        return response()->json([
            'message' => 'Admin deleted successfully',
            'redirect' => route('admins.index')
        ]);
    }

    /**
     * Get system admins.
     */
    public function systemAdmins(Request $request): JsonResponse
    {
        $admins = Admin::where('role', 'system_admin')
            ->withCount('orders')
            ->get();

        return response()->json($admins);
    }

    /**
     * Get the most recently added admin.
     */
    public function latest(Request $request): JsonResponse
    {
        $admin = Admin::latest()
            ->withCount('orders')
            ->first();

        return response()->json($admin);
    }

    /**
     * Get the total count of admins.
     */
    public function count(Request $request): JsonResponse
    {
        $count = Admin::count();
        return response()->json(['total_admins' => $count]);
    }
}