<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller {
    public function index(Request $request) {
        $tasks = $request->user()->tasks()
            ->with(['category', 'project', 'subtasks'])
            ->get();

        return response()->json([
            'message' => 'List of user tasks',
            'data' => $tasks
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'project_id' => 'nullable|exists:projects,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'deadline' => 'nullable|date',
        ]);

        // Limitation for subtask 1 level (cannot add subtask to task if have parent_id)
        if (!empty($validated['parent_id'])) {
            $parent = Task::find($validated['parent_id']);
            if ($parent->parent_id !== null) {
                return response()->json(['message' => ' 1 Level Maximum Subtask'], 422);
            }
        }

        $validated['user_id'] = $request->user()->id;

        $task = Task::create($validated);
        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    public function show(Task $task, Request $request) {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'message' => 'Task detail retrieved successfully',
            'data' => $task->load(['category', 'project', 'subtasks', 'parent'])
        ]);
    }

    public function update(Task $task, Request $request) {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'project_id' => 'nullable|exists:projects,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'deadline' => 'nullable|date',
        ]);

        // Limitation for subtask 1 level (cannot add subtask to task if have parent_id)
        if (!empty($validated['parent_id'])) {
            $parent = Task::find($validated['parent_id']);
            if ($parent->parent_id !== null) {
                return response()->json(['message' => ' 1 Level Maximum Subtask']);
            }
        }

        $task->update($validated);
        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task,
        ]);
    }

    public function destroy(Task $task, Request $request) {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->delete();
        return response()->noContent();
    }
}
