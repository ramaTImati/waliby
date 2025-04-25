<?php

namespace Ramatimati\Waliby;

use Ramatimati\Waliby\Console\SendRecurringMessages;
use Illuminate\Console\Scheduling\Schedule;
use Ramatimati\Waliby\Console\SendMessage;
use Illuminate\Support\ServiceProvider;

class WalibyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->mergeConfigFrom(__DIR__.'/config/waliby.php', 'waliby');
        $this->publishFiles();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'waliby');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SendMessage::class,
                SendRecurringMessages::class,
            ]);

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('waliby:send-message')->everyMinute();
                $schedule->command('waliby:send-recurring-messages')->hourlyAt(7);
            });
        }
    }

    private function publishFiles(){
        $publishTag = 'Waliby';

        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/vendor/'.$publishTag),
        ], $publishTag);
    }
}
