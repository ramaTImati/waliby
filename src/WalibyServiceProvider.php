<?php

namespace Ramatimati\Waliby;

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
    }

    private function publishFiles(){
        $publishTag = 'Waliby';

        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/vendor/'.$publishTag),
            __DIR__.'/App/Http/Controllers/HistoryController.php' => base_path('app/Http/Controllers/vendor/'.$publishTag.'/HistoryController.php')
        ], $publishTag);
    }
}
