<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelectedComponent\StoreSelectedComponentRequest;
use App\Http\Requests\SelectedComponent\UpdateSelectedComponentRequest;
use App\Models\Order;
use App\Models\SelectedComponent;
use App\Models\HardwareComponent;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SelectedComponentController extends Controller
{
    /**
     * Display a listing of the selected components.
     */
    public function index(): View|JsonResponse
    {
        $components = SelectedComponent::with(['order.buyer', 'hardwareComponent'])->get();
        
        if (request()->expectsJson()) {
            return response()->json($components);
        }
        
        return view('selected_components.index', compact('components'));
    }

    /**
     * Show the form for creating a new selected component.
     */
    public function create(): View
    {
        $orders = Order::with('buyer')->get();
        $components = HardwareComponent::with(['category', 'supplier'])->get();
        
        return view('selected_components.create', compact('orders', 'components'));
    }

    /**
     * Store a newly created selected component in storage.
     */
    public function store(StoreSelectedComponentRequest $request): JsonResponse
    {
        $component = SelectedComponent::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json($component->load(['order.buyer', 'hardwareComponent']), 201);
        }

        return response()->json([
            'message' => 'Component added to order successfully',
            'redirect' => route('orders.show', $component->order_id)
        ], 201);
    }

    /**
     * Store a newly created selected component for a specific order (nested route).
     */
    public function storeForOrder(StoreSelectedComponentRequest $request, Order $order): JsonResponse
    {
        $data = $request->validated();
        $data['order_id'] = $order->id;

        $component = SelectedComponent::create($data);

        if ($request->expectsJson()) {
            return response()->json($component->load(['order.buyer', 'hardwareComponent']), 201);
        }

        return response()->json([
            'message' => 'Component added to order successfully',
            'redirect' => route('orders.show', $order)
        ], 201);
    }

    /**
     * Display the specified selected component.
     */
    public function show(Order $order, SelectedComponent $selectedComponent): View|JsonResponse
    {
        // Ensure the selected component belongs to the order
        if ($selectedComponent->order_id !== $order->id) {
            abort(404);
        }

        $selectedComponent->load(['order.buyer', 'hardwareComponent']);

        if (request()->expectsJson()) {
            return response()->json($selectedComponent);
        }

        return view('selected_components.show', ['component' => $selectedComponent]);
    }

    /**
     * Show the form for editing the specified selected component.
     */
    public function edit(Order $order, SelectedComponent $selectedComponent): View
    {
        // Ensure the selected component belongs to the order
        if ($selectedComponent->order_id !== $order->id) {
            abort(404);
        }

        $selectedComponent->load(['order.buyer', 'hardwareComponent']);
        
        return view('selected_components.edit', ['component' => $selectedComponent]);
    }

    /**
     * Update the specified selected component in storage.
     */
    public function update(UpdateSelectedComponentRequest $request, Order $order, SelectedComponent $selectedComponent): JsonResponse
    {
        // Ensure the selected component belongs to the order
        if ($selectedComponent->order_id !== $order->id) {
            abort(404);
        }

        $selectedComponent->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json($selectedComponent->load(['order.buyer', 'hardwareComponent']));
        }

        return response()->json([
            'message' => 'Component quantity updated successfully',
            'redirect' => route('orders.show', $order)
        ]);
    }

    /**
     * Remove the specified selected component from storage.
     */
    public function destroy(Order $order, SelectedComponent $selectedComponent): JsonResponse
    {
        // Ensure the selected component belongs to the order
        if ($selectedComponent->order_id !== $order->id) {
            abort(404);
        }

        $selectedComponent->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Component removed from order successfully']);
        }

        return response()->json([
            'message' => 'Component removed from order successfully',
            'redirect' => route('orders.show', $order)
        ]);
    }

    /**
     * Get components by order.
     */
    public function byOrder(Order $order): JsonResponse
    {
        $components = $order->selectedComponents()
            ->with('hardwareComponent.category')
            ->get();
            
        return response()->json($components);
    }

    /**
     * Get orders containing a specific component.
     */
    public function ordersWithComponent(HardwareComponent $component): JsonResponse
    {
        $orders = Order::whereHas('selectedComponents', function ($query) use ($component) {
            $query->where('component_id', $component->id);
        })->with(['buyer', 'selectedComponents.hardwareComponent'])
        ->get();
            
        return response()->json($orders);
    }

    /**
     * Get most ordered components.
     */
    public function mostOrdered(): JsonResponse
    {
        $components = SelectedComponent::select('component_id')
            ->selectRaw('SUM(quantity) as total_ordered')
            ->with('hardwareComponent.category')
            ->groupBy('component_id')
            ->orderByDesc('total_ordered')
            ->take(10)
            ->get();
            
        return response()->json($components);
    }
}