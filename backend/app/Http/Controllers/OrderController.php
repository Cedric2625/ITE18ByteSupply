<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Admin;
use App\Models\Buyer;
use App\Models\HardwareComponent;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(\Illuminate\Http\Request $request): View|JsonResponse
    {
        $query = Order::with([
            'buyer', 
            'admin', 
            'selectedComponents.hardwareComponent' => function($query) {
                $query->with(['category', 'supplier']);
            }
        ])->latest('order_date');

        if ($request->filled('buyer')) {
            $query->where('buyer_id', $request->input('buyer'));
        }
        if ($request->filled('status')) {
            $query->where('shipping_status', $request->input('status'));
        }
        if ($request->filled('start_date')) {
            $query->where('order_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->where('order_date', '<=', $request->input('end_date'));
        }

        $perPage = $request->input('per_page', 15);
        $orders = $query->paginate($perPage)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($orders);
        }

        $buyers = Buyer::orderBy('buyer_name')->get();
        $statuses = ['pending','processing','shipped','delivered','cancelled'];
        return view('orders.index', compact('orders','buyers','statuses'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): View
    {
        $buyers = Buyer::all();
        $admins = Admin::all();
        $components = HardwareComponent::with(['category', 'supplier'])->get();
        
        return view('orders.create', compact('buyers', 'admins', 'components'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $selectedComponents = $validated['selected_components'];
        unset($validated['selected_components']);

        try {
            DB::beginTransaction();

            $order = Order::create($validated);

            // Create selected components
            foreach ($selectedComponents as $component) {
                $order->selectedComponents()->create([
                    'component_id' => $component['component_id'],
                    'quantity' => $component['quantity'],
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json($order->load([
                    'selectedComponents.hardwareComponent' => function($query) {
                        $query->with(['category', 'supplier']);
                    }
                ]), 201);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create order'], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View|JsonResponse
    {
        $order->load([
            'buyer', 
            'admin', 
            'selectedComponents.hardwareComponent' => function($query) {
                $query->with(['category', 'supplier']);
            }
        ]);

        if (request()->expectsJson()) {
            return response()->json($order);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order): View
    {
        $buyers = Buyer::all();
        $admins = Admin::all();
        $components = HardwareComponent::with(['category', 'supplier'])->get();
        $order->load([
            'selectedComponents.hardwareComponent' => function($query) {
                $query->with(['category', 'supplier']);
            }
        ]);
        
        return view('orders.edit', compact('order', 'buyers', 'admins', 'components'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse|RedirectResponse
    {
        // Check if order is locked (cancelled or completed)
        $currentStatus = $order->shipping_status;
        if ($currentStatus === 'cancelled' || $currentStatus === 'canceled' || $currentStatus === 'completed') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This order cannot be edited. Orders with status "Cancelled" or "Completed" are locked and cannot be modified.',
                    'status' => 'error'
                ], 422);
            }
            return redirect()->back()
                ->with('error', 'This order cannot be edited. Orders with status "Cancelled" or "Completed" are locked and cannot be modified.');
        }

        $validated = $request->validated();
        $selectedComponents = $validated['selected_components'] ?? null;
        unset($validated['selected_components']);

        // Check if trying to update to cancelled or completed from a locked status
        if (isset($validated['shipping_status'])) {
            $newStatus = $validated['shipping_status'];
            if (($newStatus === 'cancelled' || $newStatus === 'canceled' || $newStatus === 'completed') && 
                ($currentStatus === 'cancelled' || $currentStatus === 'canceled' || $currentStatus === 'completed')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'This order cannot be edited. Orders with status "Cancelled" or "Completed" are locked and cannot be modified.',
                        'status' => 'error'
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', 'This order cannot be edited. Orders with status "Cancelled" or "Completed" are locked and cannot be modified.');
            }
        }

        try {
            DB::beginTransaction();

            $order->update($validated);

            // Update selected components if provided
            if ($selectedComponents) {
                // Delete existing selected components
                $order->selectedComponents()->delete();

                // Create new selected components
                foreach ($selectedComponents as $component) {
                    $order->selectedComponents()->create([
                        'component_id' => $component['component_id'],
                        'quantity' => $component['quantity'],
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json($order->load([
                    'selectedComponents.hardwareComponent' => function($query) {
                        $query->with(['category', 'supplier']);
                    }
                ]));
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update order'], 500);
        }
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order): JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Delete selected components first
            $order->selectedComponents()->delete();
            
            // Then delete the order
            $order->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json(['message' => 'Order deleted successfully']);
            }

            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete order'], 500);
        }
    }

    /**
     * Get orders by buyer.
     */
    public function byBuyer(Buyer $buyer): JsonResponse
    {
        $orders = $buyer->orders()
            ->with([
                'selectedComponents.hardwareComponent' => function($query) {
                    $query->with(['category', 'supplier']);
                }
            ])
            ->get();
            
        return response()->json($orders);
    }

    /**
     * Get orders by shipping status.
     */
    public function byStatus(string $status): JsonResponse
    {
        $orders = Order::where('shipping_status', $status)
            ->with([
                'buyer', 
                'selectedComponents.hardwareComponent' => function($query) {
                    $query->with(['category', 'supplier']);
                }
            ])
            ->get();
            
        return response()->json($orders);
    }

    /**
     * Get orders by date range.
     */
    public function byDateRange(string $start, string $end): JsonResponse
    {
        $orders = Order::whereBetween('order_date', [$start, $end])
            ->with([
                'buyer', 
                'selectedComponents.hardwareComponent' => function($query) {
                    $query->with(['category', 'supplier']);
                }
            ])
            ->get();
            
        return response()->json($orders);
    }

    /**
     * Get orders statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_amount' => Order::sum('total_amount'),
            'average_amount' => Order::avg('total_amount'),
            'status_counts' => Order::select('shipping_status', DB::raw('count(*) as count'))
                ->groupBy('shipping_status')
                ->get(),
            'recent_orders' => Order::with(['buyer'])
                ->latest('order_date')
                ->take(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}