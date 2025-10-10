<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'project_id',
        'parent_id',
        'title',
        'description',
        'status',
        'priority',
        'deadline',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function parent() {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks() {
        return $this->hasMany(Task::class, 'parent_id');
    }
}
