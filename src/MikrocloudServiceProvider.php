<?php

namespace Mikrocloud\Mikrocloud;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mikrocloud\Mikrocloud\Console\CheckInstallationCommand;
use Mikrocloud\Mikrocloud\Console\CustomerModelCommand;
use Mikrocloud\Mikrocloud\Http\Middleware\Auth0Users;

class MikrocloudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @throws \Exception
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerCommands();
        $this->registerMiddleware();
        // $this->registerLogger(); WIP

    }

    /**
     * Register any application services.
     */
    public function register(): void
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
            __DIR__.'/../config/mikrocloud.php', 'mikrocloud'
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
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
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
            $this->commands([
                CustomerModelCommand::class,
                CheckInstallationCommand::class,
            ]);
        }
    }

    protected function registerLogger()
    {
        //        $logManager = $this->app->make('log');
        //        $logManager->extend('stack', function ($app, array $config) {
        //            return new LogEater($config['level'] ?? 'debug');
        //        });
    }

    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('mikrocloud', Auth0Users::class);
    }
}
