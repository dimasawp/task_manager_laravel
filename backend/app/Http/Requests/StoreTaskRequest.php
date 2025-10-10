<?php

namespace App\Http\Requests;

use App\Rules\OneLevelSubtask;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'project_id' => 'nullable|exists:projects,id',
            'parent_id' => ['nullable', 'exists:tasks,id', new OneLevelSubtask()],
            'deadline' => 'nullable|date',
        ];
    }
}
