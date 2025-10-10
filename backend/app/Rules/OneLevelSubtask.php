<?php

namespace App\Rules;

use App\Models\Task;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OneLevelSubtask implements ValidationRule {
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if ($value !== null) {
            $parent = Task::find($value);
            if ($parent && $parent->parent_id !== null) {
                $fail('1 Level Maximum Subtask');
            }
        }
    }
}
