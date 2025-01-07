<?php

namespace Altostrat\Tools\Console;

use Illuminate\Console\Command;

class CheckInstallationCommand extends Command
{
    protected $signature = 'altostrat:check';

    protected $description = 'Check if Altostrat is installed correctly';

    public function handle(): void
    {

        $customerModel = config('altostrat.customer_model');

        if (! class_exists($customerModel)) {
            $this->error('The customer model does not exist');

            return;
        }

        if (! is_subclass_of($customerModel, \Altostrat\Tools\Models\Customer::class)) {
            $this->error('The customer model does not extend the Mikrocloud customer model');

            return;
        }

        if (! config('altostrat.auth0.client_id')) {
            $this->error('The AUTH0_CLIENT_ID environment variable is not set');

            return;
        }

        if (! config('altostrat.auth0.cookie_secret')) {
            $this->error('The AUTH0_COOKIE_SECRET environment variable is not set');

            return;
        }

        if (config('altostrat.api_prefix') === 'api') {
            $this->error('The API_PREFIX environment variable is not set');

            return;
        }

        $routes_file = app()->basePath('routes/authenticated.php');

        if (! file_exists($routes_file)) {
            $this->error('The routes/authenticated.php file does not exist');

            return;
        }

        $this->info('Altostrat is installed correctly');
    }
}
