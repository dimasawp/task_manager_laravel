<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    use AuthorizesRequests;

    public function index(Request $request) {
        $categories = $request->user()->categories()
            ->latest()
            ->get();

        return response()->json([
            'message' => 'List of categories',
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function store(StoreCategoryRequest $request) {
        $validated = $request->validated();

        $category = $request->user()->categories()->create($validated);
        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(Category $category, Request $request) {
        $this->authorize('view', $category);

        return response()->json([
            'message' => 'Category Detail',
            'data' => new CategoryResource($category),
        ]);
    }

    public function update(Category $category, UpdateCategoryRequest $request) {
        $this->authorize('update', $category);
        $validated = $request->validated();

        $category->update($validated);
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category),
        ]);
    }

    public function destroy(Category $category, Request $request) {
        $this->authorize('delete', $category);

        $category->delete();
        return response()->noContent();
    }
}
