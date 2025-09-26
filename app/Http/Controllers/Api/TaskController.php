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
        return $request->user()->tasks()->create($request->all());
    }

    public function show(Task $task) {
        return $task;
    }

    public function update(Request $request, Task $task) {
        $task->update($request->all());
        return $task;
    }

    public function destroy(Task $task) {
        $task->delete();
        return response()->noContent();
    }
}
