<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'data' => $categories
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:30'
        ]);

        $category = $request->user()->categories()->create($validated);
        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function show(Category $category, Request $request) {
        $this->authorize('view', $category);

        return response()->json([
            'message' => 'Category Detail',
            'data' => $category,
        ]);
    }

    public function update(Category $category, Request $request) {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:30'
        ]);

        $category->update($validated);
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy(Category $category, Request $request) {
        $this->authorize('delete', $category);

        $category->delete();
        return response()->noContent();
    }
}
