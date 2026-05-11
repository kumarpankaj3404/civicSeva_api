<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * GET /api/v1/categories
     * Public: List all active categories.
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    /**
     * POST /api/v1/categories
     * Admin only: Create a category.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:100'],
            'icon'        => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ]);

        $category = Category::create($request->only(
            'name', 'slug', 'icon', 'description', 'is_active'
        ) + ['is_active' => $request->boolean('is_active', true)]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data'    => $category,
        ], 201);
    }

    /**
     * DELETE /api/v1/categories/{id}
     * Admin only: Deactivate a category.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Category deactivated.',
        ]);
    }
}
