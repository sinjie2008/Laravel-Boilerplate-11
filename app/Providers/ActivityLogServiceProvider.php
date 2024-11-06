<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class ActivityLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(Login::class, function ($event) {
            activity()
                ->causedBy($event->user)
                ->log("User {$event->user->name} logged in");
        });

        Event::listen(Logout::class, function ($event) {
            activity()
                ->causedBy($event->user)
                ->log("User {$event->user->name} logged out");
        });
    }
}
