<?php

namespace App\Providers;

use App\Events\DeadlineCheckEvent;
use App\Listeners\SendDeadlineNotificationListener;
// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
    protected $listen = [
        DeadlineCheckEvent::class => [SendDeadlineNotificationListener::class],
    ];

    /**
     * Register services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        parent::boot();
    }
}
