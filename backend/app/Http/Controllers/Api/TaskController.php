<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
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
            'data' => TaskResource::collection($tasks),
        ]);
    }

    public function store(StoreTaskRequest $request) {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $task = Task::create($validated);
        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task)
        ], 201);
    }

    public function show(Task $task, Request $request) {
        $this->authorize('view', $task);

        return response()->json([
            'message' => 'Task detail retrieved successfully',
            'data' => new TaskResource($task->load(['category', 'project', 'subtasks', 'parent'])),
        ]);
    }

    public function update(Task $task, UpdateTaskRequest $request) {
        $this->authorize('update', $task);
        $validated = $request->validated();

        $task->update($validated);
        return response()->json([
            'message' => 'Task updated successfully',
            'data' => new TaskResource($task),
        ]);
    }

    public function destroy(Task $task, Request $request) {
        $this->authorize('delete', $task);

        $task->delete();
        return response()->noContent();
    }
}
