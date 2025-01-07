<?php

namespace Altostrat\Tools;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Altostrat\Tools\Console\CheckInstallationCommand;
use Altostrat\Tools\Console\CustomerModelCommand;
use Altostrat\Tools\Http\Middleware\Auth0Users;
use Altostrat\Tools\Http\Middleware\ForceJson;

class AltostratServiceProvider extends ServiceProvider
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
        // $this->registerLogger();
        $this->registerPublishing();

    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configure();
    }

    /**
     * Setup the configuration for Altostrat.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/altostrat.php', 'altostrat'
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
            'prefix' => config('altostrat.api_prefix'),
            'namespace' => 'Altostrat\Tools\Http\Controllers',
            'as' => 'altostrat.',
            'middleware' => ['altostrat', 'json'],
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });

        $routes_file = app()->basePath('routes/authenticated.php');

        if (file_exists($routes_file)) {
            Route::group([
                'prefix' => config('altostrat.api_prefix'),
                'as' => 'auth0.',
                'middleware' => ['altostrat', 'json'],
            ], function () {
                $this->loadRoutesFrom($this->app->basePath('routes/authenticated.php'));
            });
        }
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

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../routes/authenticated.php' => $this->app->basePath('routes/authenticated.php'),
            ], 'altostrat-routes');
            $this->publishes([
                __DIR__ . '/../views' => $this->app->basePath('resources/views/vendor'),
            ], 'altostrat-mail-template');
        }
    }

    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('altostrat', Auth0Users::class);
        $this->app['router']->aliasMiddleware('json', ForceJson::class);
    }
}
