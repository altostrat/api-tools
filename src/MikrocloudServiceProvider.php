<?php

namespace Mikrocloud\Mikrocloud;

use Illuminate\Support\Facades\Route;
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
        $this->registerRoutes();
        $this->registerCommands();

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
    }

    /**
     * Setup the configuration for MikroCloud.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/mikrocloud.php', 'mikrocloud'
        );
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('mikrocloud.key'),
            'namespace' => 'Mikrocloud\Mikrocloud\Http\Controllers',
            'as' => 'mikrocloud.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
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