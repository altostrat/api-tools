<?php

namespace Mikrocloud\Mikrocloud\Console;

use Illuminate\Console\Command;

class CustomerModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mikrocloud:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs assets for MikroCloud';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customerModel = config('mikrocloud.customer_model');
        $customerModel = str_replace('App', '', $customerModel);
        $customerModel = str_replace('\\', '/', $customerModel);

        $model = __DIR__.$customerModel.'Model.php';
        $model = str_replace('/src/Console/', '/src/', $model);

        $destination = app_path($customerModel.'.php');

        if (file_exists($destination)) {
            $this->warn('Customer model already exists in '.$destination);

        } else {
            copy($model, $destination);

            $this->info('Customer model installed successfully in '.$destination);
        }

        if ($this->confirm('Do you want to install the laravel/octane package?')) {

            $this->info('Installing laravel/octane...');
            exec('composer require laravel/octane');
            $this->info('laravel/octane installed successfully');

            if ($this->confirm('Do you want to run the octane install command?')) {
                $this->info('Running php artisan octane:install...');
                $this->call('octane:install');
                $this->info('php artisan octane:install ran successfully');
            }
        }

        if ($this->confirm('Do you want to install the laravel/vapor-core package?')) {

            $this->info('Installing laravel/vapor-core...');
            exec('composer require laravel/vapor-core');
            $this->info('laravel/vapor-core installed successfully');

            if ($this->confirm('Do you want to run the vapor:install command?')) {
                $this->info('Running php artisan vapor:install...');
                $this->call('vapor:install');
                $this->info('php artisan vapor:install ran successfully');
            }
        }

        // if routes/authenticated.php does not exist in the laravel app, create it
        $routes_file = app()->basePath('routes/authenticated.php');

        if (! file_exists($routes_file)) {
            $this->info('Creating routes/authenticated.php...');
            $this->call('vendor:publish', [
                '--tag' => 'mikrocloud-routes',
                '--force' => true,
            ]);
            $this->info('routes/authenticated.php created successfully');
        }
    }
}
