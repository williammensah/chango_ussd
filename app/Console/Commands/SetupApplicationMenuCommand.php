<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupApplicationMenuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates menu data for momo teller application';

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
        $menus = [
            [
                'name' => 'main_menu',
                'content' => "Welcome to Chango" .PHP_EOL. " Public Group". PHP_EOL."1.Enter Campaign Code",
            ],
            [
                'name' => 'campaign_alias',
                'content' => "Enter Campaign Alias".PHP_EOL,
            ],
            [
                'name' => 'campaign_name',
                'content' => "{data}".PHP_EOL."Target: {target}" .PHP_EOL."Collected: {Collected}".PHP_EOL."1.Donate".PHP_EOL."0.Back",
            ],
            [
                'name' => 'donate',
                'content' => "Donate to {data}".PHP_EOL."Enter Amount",
            ],
            [
                'name' => 'authorize_payment',
                'content' => "Please authorize payment",
            ],
           
        ];
        \DB::table('ussd_menus')->delete();
        \DB::table('ussd_menus')->insert($menus);
        Artisan::call('cache:clear');
        echo "<br>Done successfully. Cache cleared<br>";
        return "Done";
    }
}
