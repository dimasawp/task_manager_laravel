<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Policies\CategoryPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Project::class => ProjectPolicy::class,
        Task::class => TaskPolicy::class,
    ];
    /**
     * Register services. (Service selain AuthServiceProvider wajib daftar)
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        // Bawaan provider khusus AuthServiceProvider (bukan provider biasa), otomatis mendaftarkan variable $policies
        $this->registerPolicies();
    }
}
