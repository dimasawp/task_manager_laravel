<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy {
    public function view(User $user, Project $project): bool {
        return $user->id === $project->created_by;
    }

    public function update(User $user, Project $project): bool {
        return $user->id === $project->created_by;
    }

    public function delete(User $user, Project $project): bool {
        return $user->id === $project->created_by;
    }
}
