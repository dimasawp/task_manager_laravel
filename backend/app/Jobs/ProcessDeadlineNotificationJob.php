<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DeadlineTodayNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDeadlineNotificationJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $projects;
    protected $tasks;

    /**
     * Create a new job instance.
     */
    public function __construct($projects, $tasks) {
        $this->projects = $projects;
        $this->tasks = $tasks;
    }

    /**
     * Execute the job.
     */
    public function handle(): void {
        // Get user who creates project/task
        $userIds = collect()
            ->merge($this->projects->pluck('created_by'))
            ->merge($this->tasks->pluck('user_id'))
            ->unique();

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $user->notify(new DeadlineTodayNotification(
                $this->projects->where('created_by', $user->id),
                $this->tasks->where('user_id', $user->id)
            ));
        }
    }
}
