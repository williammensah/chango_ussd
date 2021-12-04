<?php

namespace App\Console\Commands;

// use App\RabbitMQ\RabbitMQBaseConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshMenusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh menus cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call('cache:clear');
        echo "Done";
    }
}
