<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectController extends Controller {
    use AuthorizesRequests;

    public function index(Request $request) {
        $projects = $request->user()->projects()
            ->with('tasks')
            ->get();

        return response()->json([
            'message' => 'List of projects',
            'data' => $projects
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
        ]);

        $validated['created_by'] = $request->user()->id;

        $project = Project::create($validated);
        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project
        ], 201);
    }

    public function show(Project $project, Request $request) {
        $this->authorize('view', $project);

        return response()->json([
            'message' => 'Task detail retrieved successfully',
            'data' => $project->load('tasks'),
        ]);
    }

    public function update(Project $project, Request $request) {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
        ]);

        $project->update($validated);
        return response()->json([
            'message' => 'Project updated successfully',
            'data' => $project,
        ]);
    }

    public function destroy(Project $project, Request $request) {
        $this->authorize('delete', $project);

        $project->delete();
        return response()->noContent();
    }
}
