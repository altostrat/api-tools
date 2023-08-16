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
            $this->error('Customer model already exists in '.$destination);

            return;
        }

        copy($model, $destination);

        $this->info('Customer model installed successfully in '.$destination);
    }
}
