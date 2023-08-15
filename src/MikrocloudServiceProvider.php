<?php

namespace Mikrocloud\Mikrocloud;

use Illuminate\Support\ServiceProvider;

class MikrocloudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->registerRoutes();
        $this->registerCommands();
    }

    /**
     * Setup the configuration for MikroCloud.
     *
     * @return void
     */
    protected function configure()
    {
        //
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        //
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            //
        }
    }
}