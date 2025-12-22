<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display a listing of the suppliers.
     */
    public function index(Request $request): View|JsonResponse
    {
        $suppliers = Supplier::withCount('hardwareComponents')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        if ($request->expectsJson()) {
            return response()->json($suppliers);
        }
        
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create(): View
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json($supplier, 201);
        }

        return response()->json([
            'message' => 'Supplier created successfully',
            'redirect' => route('suppliers.show', $supplier)
        ], 201);
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier): View|JsonResponse
    {
        $supplier->load('hardwareComponents');

        if (request()->expectsJson()) {
            return response()->json($supplier);
        }

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json($supplier);
        }

        return response()->json([
            'message' => 'Supplier updated successfully',
            'redirect' => route('suppliers.show', $supplier)
        ]);
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Supplier deleted successfully']);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Get suppliers with hardware components count.
     */
    public function withComponentsCount(): JsonResponse
    {
        $suppliers = Supplier::withCount('hardwareComponents')
            ->having('hardware_components_count', '>', 0)
            ->get();
            
        return response()->json($suppliers);
    }

    /**
     * Get suppliers without any hardware components.
     */
    public function inactive(): JsonResponse
    {
        $suppliers = Supplier::doesntHave('hardwareComponents')->get();
        return response()->json($suppliers);
    }
}