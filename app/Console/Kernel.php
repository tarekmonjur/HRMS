<?php

namespace App\Console;

use App\Console\Commands\ServiceCommand;
use App\Console\Commands\SalaryIncrementCommand;
use App\Console\Commands\AttendanceTimesheetCommand;
use App\Console\Commands\ArchiveAttendanceTimesheetCommand;
use App\Console\Commands\CalculateEarnLeave;
use App\Console\Commands\MakeWeekendActive;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

        if(\Schema::hasTable('configs'))
        {
            $databases = DB::table('configs')->get();

            foreach($databases as $database){
                $schedule->command('active:weekend '.$database->database_name)
                        // ->timezone('Asia/Dhaka')
                        // ->cron('* * * * * *');
                        ->twiceDaily(1, 13);

                $schedule->command('calculate:earnLeave '.$database->database_name)
                        // ->timezone('Asia/Dhaka')
                        // ->cron('* * * * * *');
                        ->twiceDaily(1, 13);

                $schedule->command('attendance:timesheet '.$database->database_name)
                    // ->cron('* * * * * *');
                     ->twiceDaily(1, 13);
            
                $schedule->command('attendance:archive '.$database->database_name)
                        // ->cron('* * * * * *');
                         ->twiceDaily(1, 13);

                $schedule->command('salary:increment '.$database->database_name)
                    // ->cron('* * * * * *');
                     ->twiceDaily(1, 13);
            }
        }

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
