<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HardwareComponent;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\SelectedComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    /**
     * Get shop products (public - no auth required)
     */
    public function index(Request $request)
    {
        try {
            $query = HardwareComponent::with(['category', 'supplier'])->latest();
            
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }
            if ($request->filled('supplier')) {
                $query->where('supplier_id', $request->supplier);
            }
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function($q) use ($s) {
                    $q->where('component_name', 'like', "%$s%")
                      ->orWhere('brand', 'like', "%$s%")
                      ->orWhere('model', 'like', "%$s%")
                      ->orWhere('component_reference_number', 'like', "%$s%");
                });
            }
            
            $components = $query->paginate(12);
            $categories = Category::orderBy('category_name')->get();
            $suppliers = Supplier::orderBy('supplier_name')->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $components->items(),
                'meta' => [
                    'current_page' => $components->currentPage(),
                    'last_page' => $components->lastPage(),
                    'per_page' => $components->perPage(),
                    'total' => $components->total(),
                ],
                'categories' => $categories,
                'suppliers' => $suppliers,
            ]);
        } catch (\Exception $e) {
            Log::error('Shop Index Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products',
            ], 500);
        }
    }

    /**
     * Get cart items (stored in database for authenticated users)
     */
    public function cart(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'success',
                    'data' => []
                ]);
            }

            // Get cart from database (cart_items table) or use session-based approach
            // For now, we'll use a simple approach: store cart in user's session via API
            // In production, you might want a cart_items table
            
            // For API, we'll return empty cart and let frontend manage it
            // Or we can store cart in database
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        } catch (\Exception $e) {
            Log::error('Cart Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch cart',
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        try {
            $validated = $request->validate([
                'component_id' => 'required|exists:hardware_components,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $component = HardwareComponent::findOrFail($validated['component_id']);
            
            // Check stock
            if ($component->stock_quantity < $validated['quantity']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient stock available',
                ], 400);
            }

            // For API, cart is managed client-side
            // Return success so frontend can update its cart
            return response()->json([
                'status' => 'success',
                'message' => 'Added to cart',
                'component' => [
                    'id' => $component->id,
                    'name' => $component->component_name,
                    'price' => (float) $component->retail_price,
                    'stock_quantity' => $component->stock_quantity,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Add to Cart Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add to cart',
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request, $id)
    {
        try {
            // For API, cart is managed client-side
            return response()->json([
                'status' => 'success',
                'message' => 'Removed from cart',
            ]);
        } catch (\Exception $e) {
            Log::error('Remove from Cart Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove from cart',
            ], 500);
        }
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            $validated = $request->validate([
                'payment_method' => 'required|in:cash,credit_card,bank_transfer,online_payment',
                'items' => 'required|array|min:1',
                'items.*.component_id' => 'required|exists:hardware_components,id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            return DB::transaction(function () use ($request, $validated, $user) {
                // Calculate total
                $total = 0;
                $items = [];
                
                foreach ($validated['items'] as $item) {
                    $component = HardwareComponent::findOrFail($item['component_id']);
                    
                    // Check stock
                    if ($component->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$component->component_name}");
                    }
                    
                    $subtotal = $component->retail_price * $item['quantity'];
                    $total += $subtotal;
                    
                    $items[] = [
                        'component' => $component,
                        'quantity' => $item['quantity'],
                        'subtotal' => $subtotal,
                    ];
                }

                // Create order
                $order = Order::create([
                    'order_date' => now(),
                    'total_amount' => $total,
                    'shipping_status' => 'pending',
                    'tracking_number' => null,
                    'estimated_delivery' => now()->addDays(7),
                    'order_reference_number' => strtoupper(str()->random(12)),
                    'buyer_id' => $user->id,
                    'admin_id' => 1, // Default admin
                    'payment_method' => $validated['payment_method'],
                ]);

                // Create selected components and reduce stock
                foreach ($items as $item) {
                    SelectedComponent::create([
                        'order_id' => $order->id,
                        'component_id' => $item['component']->id,
                        'quantity' => $item['quantity'],
                    ]);
                    
                    $item['component']->decrement('stock_quantity', $item['quantity']);
                }

                $order->load(['selectedComponents.hardwareComponent', 'buyer']);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Order placed successfully',
                    'order' => $order,
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error('Place Order Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Failed to place order',
            ], 500);
        }
    }

    /**
     * Get buyer's orders
     */
    public function myOrders(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            $query = Order::where('buyer_id', $user->id)
                ->with(['selectedComponents.hardwareComponent', 'buyer']);

            // Filter by status if provided
            if ($request->filled('status')) {
                $query->where('shipping_status', $request->input('status'));
            }

            // Exclude completed orders if exclude_completed is true
            if ($request->boolean('exclude_completed')) {
                $query->where('shipping_status', '!=', 'completed');
            }

            $orders = $query->latest('order_date')
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $orders->items(),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('My Orders Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch orders',
            ], 500);
        }
    }
}

