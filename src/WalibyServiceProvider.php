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
        $this->app->singleton(Waliby::class, function(){
            return new Waliby();
        });
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
        $this->loadMigrationSFrom(__DIR__.'database/migrations');
    }

    private function publishFiles(){
        $publishTag = 'Waliby';

        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/vendor/'.$publishTag),
        ], $publishTag);
    }
}
