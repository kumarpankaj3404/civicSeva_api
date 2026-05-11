<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchemeRequest;
use App\Http\Requests\UpdateSchemeRequest;
use App\Models\Scheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchemeController extends Controller
{
    /**
     * GET /api/v1/schemes
     * Public: List all active schemes (paginated, filterable, searchable).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Scheme::active();

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('tags')) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $query->whereIn('tags', $tags);
        }

        $perPage = min($request->integer('per_page', 15), 50);
        $schemes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $schemes->items(),
            'meta'    => [
                'current_page' => $schemes->currentPage(),
                'per_page'     => $schemes->perPage(),
                'total'        => $schemes->total(),
                'last_page'    => $schemes->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/v1/schemes/{id}
     * Public: Show a single scheme.
     */
    public function show(string $id): JsonResponse
    {
        $scheme = Scheme::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $scheme,
        ]);
    }

    /**
     * POST /api/v1/schemes
     * Admin only: Create a new scheme.
     */
    public function store(StoreSchemeRequest $request): JsonResponse
    {
        $scheme = Scheme::create(array_merge(
            $request->validated(),
            ['is_active' => $request->boolean('is_active', true)]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Scheme created successfully.',
            'data'    => $scheme,
        ], 201);
    }

    /**
     * PUT /api/v1/schemes/{id}
     * Admin only: Update a scheme.
     */
    public function update(UpdateSchemeRequest $request, string $id): JsonResponse
    {
        $scheme = Scheme::findOrFail($id);
        $scheme->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Scheme updated successfully.',
            'data'    => $scheme->fresh(),
        ]);
    }

    /**
     * DELETE /api/v1/schemes/{id}
     * Admin only: Soft-deactivate a scheme.
     */
    public function destroy(string $id): JsonResponse
    {
        $scheme = Scheme::findOrFail($id);
        $scheme->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Scheme deactivated successfully.',
        ]);
    }
}
