<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller {
    public function index(Request $request) {
        return $request->user()->tasks()->with('category')->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|exists:categories,id',
            'priority' => 'nullable|in:low,medium,high'
        ]);
        return $request->user()->tasks()->create($validated);
    }

    public function show(Task $task, Request $request) {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $task;
    }

    public function update(Request $request, Task $task) {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string',
            'category' => 'nullable|exists:categories,id',
            'priority' => 'nullable|in:low,medium,high'
        ]);
        $task->update($validated);
        return $task;
    }

    public function destroy(Task $task, Request $request) {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $task->delete();
        return response()->noContent();
    }
}
