<?php

namespace Mikrocloud\Mikrocloud\Console;

use Illuminate\Console\Command;

class CheckInstallationCommand extends Command
{
    protected $signature = 'mikrocloud:check';

    protected $description = 'Check if MikroCloud is installed correctly';

    public function handle(): void
    {

        $customerModel = config('mikrocloud.customer_model');
        if (! class_exists($customerModel)) {
            $this->error('The customer model does not exist');

            return;
        }

        if (! is_subclass_of($customerModel, \Mikrocloud\Mikrocloud\Models\Customer::class)) {
            $this->error('The customer model does not extend the Mikrocloud customer model');

            return;
        }

        if (! config('mikrocloud.auth0.client_id')) {
            $this->error('The AUTH0_CLIENT_ID environment variable is not set');

            return;
        }

        if (! config('mikrocloud.auth0.cookie_secret')) {
            $this->error('The AUTH0_COOKIE_SECRET environment variable is not set');

            return;
        }

        $this->info('MikroCloud is installed correctly');
    }
}
