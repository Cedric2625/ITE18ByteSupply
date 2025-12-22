<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request): View|JsonResponse
    {
        $categories = Category::withCount('hardwareComponents')
            ->with(['hardwareComponents' => function ($query) {
                $query->latest()->take(5);
            }])
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        if ($request->expectsJson()) {
            return response()->json($categories);
        }
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json($category, 201);
        }

        return response()->json([
            'message' => 'Category created successfully',
            'redirect' => route('categories.show', $category)
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Request $request, Category $category): View|JsonResponse
    {
        $category->load(['hardwareComponents' => function ($query) {
            $query->with(['supplier', 'selectedComponents.order'])
                ->withCount('selectedComponents')
                ->latest();
        }]);

        if ($request->expectsJson()) {
            return response()->json($category);
        }

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json($category);
        }

        return response()->json([
            'message' => 'Category updated successfully',
            'redirect' => route('categories.show', $category)
        ]);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        try {
            $categoryName = $category->category_name;
            $category->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Category deleted successfully',
                    'data' => [
                        'id' => $category->id,
                        'category_name' => $categoryName
                    ]
                ]);
            }

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Category delete error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete category: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('categories.index')
                ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Get categories with hardware components count.
     */
    public function withComponentsCount(Request $request): JsonResponse
    {
        $categories = Category::withCount('hardwareComponents')
            ->having('hardware_components_count', '>', 0)
            ->with(['hardwareComponents' => function ($query) {
                $query->withCount('selectedComponents')
                    ->with('supplier')
                    ->latest();
            }])
            ->get();
            
        return response()->json($categories);
    }

    /**
     * Get empty categories.
     */
    public function empty(Request $request): JsonResponse
    {
        $categories = Category::doesntHave('hardwareComponents')
            ->withCount('hardwareComponents')
            ->get();

        return response()->json($categories);
    }
}