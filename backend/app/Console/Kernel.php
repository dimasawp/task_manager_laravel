<?php

namespace App\Console;

use App\Events\DeadlineCheckEvent;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    protected function schedule(Schedule $schedule): void {
        $schedule->call(function () {
            $today = now()->toDateString();

            $projects = Project::whereDate('deadline', $today)->get();
            $tasks = Task::whereDate('deadline', $today)->get();

            if ($projects->isNotEmpty() || $tasks->isNotEmpty()) {
                event(new DeadlineCheckEvent($projects, $tasks));
            }
        })->dailyAt('00:00');
    }

    protected function commands(): void {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
