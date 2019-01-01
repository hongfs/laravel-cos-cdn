<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->exec(config('cdn.python_name') . ' ' . base_path('python/index.py'))
            ->cron(config('cdn.cron'))          // 任务时间
            ->timezone(config('app.timezone'))  // 时区
            ->withoutOverlapping()              // 避免同时运行
            ->before(function() {
                info('开始执行更新任务');
            })
            ->after(function() {
                info('执行更新任务结束');
            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
