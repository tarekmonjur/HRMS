<?php

namespace App\Console;

use App\Console\Commands\ServiceCommand;
use App\Console\Commands\SalaryIncrementCommand;
use App\Console\Commands\AttendanceTimesheetCommand;
use App\Console\Commands\ArchiveAttendanceTimesheetCommand;
use App\Console\Commands\CalculateEarnLeave;
use App\Console\Commands\MakeWeekendActive;

use Illuminate\Support\Facades\Artisan;
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
        ServiceCommand::class,
        CalculateEarnLeave::class,
        MakeWeekendActive::class,
        SalaryIncrementCommand::class,
        AttendanceTimesheetCommand::class,
        ArchiveAttendanceTimesheetCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('active:weekend')
                 ->timezone('Asia/Dhaka')
                 ->everyMinute();

        // $schedule->command('calculate:earnLeave')
        //          ->timezone('Asia/Dhaka')
        //          ->everyMinute();
        

        $schedule->command('attendance:timesheet',['dbname' => '1489485338_afc_health'])->cron('* * * * * *');
        $schedule->command('attendance:archive', ['dbname' => '1489485338_afc_health'])->everyMinute();

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
