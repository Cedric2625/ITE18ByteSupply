<?php

namespace App\Http\Controllers;

use App\Http\Requests\HardwareComponent\StoreHardwareComponentRequest;
use App\Http\Requests\HardwareComponent\UpdateHardwareComponentRequest;
use App\Models\Category;
use App\Models\HardwareComponent;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HardwareComponentController extends Controller
{
    /**
     * Display a listing of the hardware components.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = HardwareComponent::with(['category', 'supplier'])
            ->withCount('selectedComponents')
            ->latest();

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->input('supplier'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('component_name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('component_reference_number', 'like', "%{$search}%");
            });
        }

        $components = $query->paginate(10)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json($components);
        }

        $categories = Category::orderBy('category_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();

        return view('hardware_components.index', compact('components', 'categories', 'suppliers'));
    }

    /**
     * Show the form for creating a new hardware component.
     */
    public function create(): View
    {
        $categories = Category::orderBy('category_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();

        return view('hardware_components.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created hardware component in storage.
     */
    public function store(StoreHardwareComponentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Convert date strings to proper format
        $validated['date_created'] = $validated['date_created'] ? date('Y-m-d', strtotime($validated['date_created'])) : null;
        $validated['date_order'] = $validated['date_order'] ? date('Y-m-d', strtotime($validated['date_order'])) : null;
        $validated['date_arrive'] = $validated['date_arrive'] ? date('Y-m-d', strtotime($validated['date_arrive'])) : null;

        $component = HardwareComponent::create($validated);

        if ($request->expectsJson()) {
            return response()->json($component, 201);
        }

        return response()->json([
            'message' => 'Hardware component created successfully',
            'redirect' => route('hardware-components.show', $component)
        ], 201);
    }

    /**
     * Display the specified hardware component.
     */
    public function show(Request $request, HardwareComponent $hardwareComponent): View|JsonResponse
    {
        $hardwareComponent->load([
            'category',
            'supplier',
            'selectedComponents.order.buyer',
        ]);

        if ($request->expectsJson()) {
            return response()->json($hardwareComponent);
        }

		return view('hardware_components.show', compact('hardwareComponent'));
    }

    /**
     * Show the form for editing the specified hardware component.
     */
    public function edit(HardwareComponent $hardwareComponent): View
    {
        $hardwareComponent->load(['category', 'supplier']);
        $categories = Category::orderBy('category_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();

        return view('hardware_components.edit', compact('hardwareComponent', 'categories', 'suppliers'));
    }

    /**
     * Update the specified hardware component in storage.
     */
    public function update(UpdateHardwareComponentRequest $request, HardwareComponent $hardwareComponent)
    {
        $validated = $request->validated();

        // Convert date strings to proper format
        $validated['date_created'] = $validated['date_created'] ? date('Y-m-d', strtotime($validated['date_created'])) : null;
        $validated['date_order'] = $validated['date_order'] ? date('Y-m-d', strtotime($validated['date_order'])) : null;
        $validated['date_arrive'] = $validated['date_arrive'] ? date('Y-m-d', strtotime($validated['date_arrive'])) : null;

        $hardwareComponent->update($validated);

        if ($request->expectsJson()) {
            return response()->json($hardwareComponent);
        }

        return redirect()
            ->route('hardware-components.show', $hardwareComponent)
            ->with('success', 'Component Updated Successfully!');
    }

    /**
     * Remove the specified hardware component from storage.
     */
    public function destroy(Request $request, HardwareComponent $hardwareComponent)
    {
        $hardwareComponent->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Hardware component deleted successfully']);
        }

        return redirect()->route('hardware-components.index')
            ->with('success', 'Hardware component deleted successfully');
    }

    /**
     * Get components by category.
     */
    public function byCategory(Request $request, Category $category): JsonResponse
    {
        $components = $category->hardwareComponents()
            ->with(['supplier'])
            ->withCount('selectedComponents')
            ->get();

        return response()->json($components);
    }

    /**
     * Get components by supplier.
     */
    public function bySupplier(Request $request, Supplier $supplier): JsonResponse
    {
        $components = $supplier->hardwareComponents()
            ->with(['category'])
            ->withCount('selectedComponents')
            ->get();

        return response()->json($components);
    }

    /**
     * Get components with low stock.
     */
    public function lowStock(Request $request): JsonResponse
    {
        $components = HardwareComponent::with(['category', 'supplier'])
            ->withCount('selectedComponents')
            ->having('selected_components_count', '>', 10)
            ->get();

        return response()->json($components);
    }

    /**
     * Get components ordered between dates.
     */
    public function orderedBetweenDates(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $components = HardwareComponent::with(['category', 'supplier'])
            ->whereBetween('date_order', [
                $request->input('start_date'),
                $request->input('end_date'),
            ])
            ->get();

        return response()->json($components);
    }
}