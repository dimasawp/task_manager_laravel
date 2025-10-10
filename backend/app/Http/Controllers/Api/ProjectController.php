<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
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
            'data' => ProjectResource::collection($projects),
        ]);
    }

    public function store(StoreProjectRequest $request) {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $project = Project::create($validated);
        return response()->json([
            'message' => 'Project created successfully',
            'data' => new ProjectResource($project)
        ], 201);
    }

    public function show(Project $project, Request $request) {
        $this->authorize('view', $project);

        return response()->json([
            'message' => 'Task detail retrieved successfully',
            'data' => new ProjectResource($project->load('tasks')),
        ]);
    }

    public function update(Project $project, UpdateProjectRequest $request) {
        $this->authorize('update', $project);
        $validated = $request->validated();

        $project->update($validated);
        return response()->json([
            'message' => 'Project updated successfully',
            'data' => new ProjectResource($project),
        ]);
    }

    public function destroy(Project $project, Request $request) {
        $this->authorize('delete', $project);

        $project->delete();
        return response()->noContent();
    }
}
