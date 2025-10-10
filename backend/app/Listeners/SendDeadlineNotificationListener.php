<?php

namespace App\Listeners;

use App\Events\DeadlineCheckEvent;
use App\Jobs\ProcessDeadlineNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDeadlineNotificationListener {
    /**
     * Create the event listener.
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DeadlineCheckEvent $event): void {
        // Kirim ke job supaya jalan di queue
        ProcessDeadlineNotificationJob::dispatch($event->projects, $event->tasks);
    }
}
