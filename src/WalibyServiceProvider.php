<?php

namespace Ramatimati\Waliby;

use Ramatimati\Waliby\Console\SendWA;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

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
                SendWA::class,
            ]);

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                // $schedule->command('waliby:send-wa')->hourlyAt(7);
                $schedule->command('waliby:send-wa')->everyMinute();
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
