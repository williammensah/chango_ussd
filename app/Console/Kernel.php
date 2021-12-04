<?php

namespace App\Console;

use App\Console\Commands\RefreshMenusCommand;
use App\Console\Commands\SetupApplicationMenuCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RefreshMenusTwoCommand;
use App\Console\Commands\SetupApplicationMenuTwoCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SetupApplicationMenuCommand::class,
        RefreshMenusCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
