<?php

namespace App\Http\Controllers;

use App\Http\Requests\Buyer\StoreBuyerRequest;
use App\Http\Requests\Buyer\UpdateBuyerRequest;
use App\Models\Buyer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerController extends Controller
{
    /**
     * Display a listing of the buyers.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Buyer::withCount('orders')
            ->with(['orders' => function ($query) {
                $query->latest('order_date')->take(5);
            }]);

        // Search filter (works for both web and API)
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('buyer_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('buyer_number', 'like', "%{$search}%");
            });
        }

        // Get per_page from request, default to 10
        $perPage = $request->integer('per_page', 10);
        $buyers = $query->latest()->paginate($perPage)->withQueryString();
        
        if ($request->expectsJson()) {
            // For API, include stats in response
            $stats = [
                'total' => Buyer::count(),
                'active' => Buyer::has('orders')->count(),
                'inactive' => Buyer::doesntHave('orders')->count(),
            ];
            // Return paginated response with stats
            return response()->json([
                'data' => $buyers->items(),
                'stats' => $stats,
                'current_page' => $buyers->currentPage(),
                'last_page' => $buyers->lastPage(),
                'per_page' => $buyers->perPage(),
                'total' => $buyers->total(),
            ]);
        }
        
        $stats = [
            'total' => Buyer::count(),
            'active' => Buyer::has('orders')->count(),
            'inactive' => Buyer::doesntHave('orders')->count(),
        ];

        return view('buyers.index', compact('buyers', 'stats'));
    }

    /**
     * Show the form for creating a new buyer.
     */
    public function create(): View
    {
        return view('buyers.create');
    }

    /**
     * Store a newly created buyer in storage.
     */
    public function store(StoreBuyerRequest $request): JsonResponse
    {
        $buyer = Buyer::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json($buyer, 201);
        }

        return response()->json([
            'message' => 'Buyer created successfully',
            'redirect' => route('buyers.show', $buyer)
        ], 201);
    }

    /**
     * Display the specified buyer.
     */
    public function show(Request $request, Buyer $buyer): View|JsonResponse
    {
        $buyer->load(['orders' => function ($query) {
            $query->with(['selectedComponents.hardwareComponent'])
                ->latest('order_date');
        }]);

        if ($request->expectsJson()) {
            return response()->json($buyer);
        }

        return view('buyers.show', compact('buyer'));
    }

    /**
     * Show the form for editing the specified buyer.
     */
    public function edit(Buyer $buyer): View
    {
        return view('buyers.edit', compact('buyer'));
    }

    /**
     * Update the specified buyer in storage.
     */
    public function update(UpdateBuyerRequest $request, Buyer $buyer): JsonResponse
    {
        $buyer->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json($buyer);
        }

        return response()->json([
            'message' => 'Buyer updated successfully',
            'redirect' => route('buyers.show', $buyer)
        ]);
    }

    /**
     * Remove the specified buyer from storage.
     */
    public function destroy(Request $request, Buyer $buyer): JsonResponse
    {
        try {
            // Check if buyer has any orders
            $orderCount = $buyer->orders()->count();
            
            if ($orderCount > 0) {
                // Check if force delete is requested
                if ($request->has('force') && $request->boolean('force')) {
                    // Delete associated orders first (cascade delete)
                    $buyer->orders()->each(function ($order) {
                        // Delete selected components first
                        $order->selectedComponents()->delete();
                        // Then delete the order
                        $order->delete();
                    });
                    
                    // Now delete the buyer
                    $buyer->delete();
                    
                    return response()->json([
                        'message' => 'Buyer and all associated orders deleted successfully'
                    ]);
                } else {
                    // Return error with option to force delete
                    return response()->json([
                        'message' => "Cannot delete buyer with {$orderCount} associated order(s). Use force delete to remove buyer and all orders.",
                        'order_count' => $orderCount,
                        'force_delete_available' => true
                    ], 422);
                }
            }

            // No orders, safe to delete
            $buyer->delete();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Buyer deleted successfully']);
            }

            return response()->json([
                'message' => 'Buyer deleted successfully',
                'redirect' => route('buyers.index')
            ]);
        } catch (\Exception $e) {
            \Log::error('Buyer delete error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete buyer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get buyers with active orders.
     */
    public function withActiveOrders(Request $request): JsonResponse
    {
        $buyers = Buyer::has('orders')
            ->withCount('orders')
            ->with(['orders' => function ($query) {
                $query->with('selectedComponents.hardwareComponent')
                    ->latest('order_date');
            }])
            ->get();

        return response()->json($buyers);
    }

    /**
     * Get the total count of buyers.
     */
    public function count(Request $request): JsonResponse
    {
        $stats = [
            'total_buyers' => Buyer::count(),
            'active_buyers' => Buyer::has('orders')->count(),
            'inactive_buyers' => Buyer::doesntHave('orders')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get the most recently added buyer.
     */
    public function latest(Request $request): JsonResponse
    {
        $buyer = Buyer::latest()->first();
        return response()->json($buyer);
    }

    /**
     * Get only active buyers (buyers that have at least one order), minimal fields.
     */
    public function activeOnly(Request $request): JsonResponse|View
    {
        $buyers = Buyer::has('orders')
            ->withCount('orders')
            ->select(['id', 'buyer_name', 'email'])
            ->orderBy('buyer_name')
            ->get();

        if ($request->expectsJson()) {
            return response()->json($buyers);
        }

        return view('buyers.active', compact('buyers'));
    }
}