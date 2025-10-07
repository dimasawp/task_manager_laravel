<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'deadline' => $this->deadline?->toDateTimeString(),
            'category' => $this->whenLoaded('category') ? new CategoryResource($this->category) : null,
            'project' => $this->whenLoaded('project') ? new ProjectResource($this->project) : null,
            'parent' => $this->whenLoaded('parent') ? new self($this->parent) : null,
            'subtasks' => self::collection($this->whenLoaded('subtasks')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
