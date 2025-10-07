<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller {
    use AuthorizesRequests;

    public function index(Request $request) {
        $tasks = $request->user()->tasks()
            ->with(['category', 'project', 'subtasks'])
            ->get();

        return response()->json([
            'message' => 'List of user tasks',
            'data' => $tasks
        ]);
    }

    public function store(StoreTaskRequest $request) {
        $validated = $request->validated();

        // Limitation for subtask 1 level (cannot add subtask to task if have parent_id)
        // if (!empty($validated['parent_id'])) {
        //     $parent = Task::find($validated['parent_id']);
        //     if ($parent->parent_id !== null) {
        //         return response()->json(['message' => ' 1 Level Maximum Subtask'], 422);
        //     }
        // }

        $validated['user_id'] = $request->user()->id;

        $task = Task::create($validated);
        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    public function show(Task $task, Request $request) {
        $this->authorize('view', $task);

        return response()->json([
            'message' => 'Task detail retrieved successfully',
            'data' => $task->load(['category', 'project', 'subtasks', 'parent'])
        ]);
    }

    public function update(Task $task, UpdateTaskRequest $request) {
        $this->authorize('update', $task);
        $validated = $request->validated();

        // Limitation for subtask 1 level (cannot add subtask to task if have parent_id)
        // if (!empty($validated['parent_id'])) {
        //     $parent = Task::find($validated['parent_id']);
        //     if ($parent->parent_id !== null) {
        //         return response()->json(['message' => ' 1 Level Maximum Subtask']);
        //     }
        // }

        $task->update($validated);
        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task,
        ]);
    }

    public function destroy(Task $task, Request $request) {
        $this->authorize('delete', $task);

        $task->delete();
        return response()->noContent();
    }
}
