<?php

namespace App\Console\Commands;

use App\Jobs\AttendanceTimesheetJob;
use Illuminate\Console\Command;

class AttendanceTimesheetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:timesheet {dbname}?';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate attendance timesheet';

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
        \Config::set('database.connections.mysql_hrms.strict',false);
        \Artisan::call("db:connect", ['database' => $this->argument('dbname')]);
        dispatch(new AttendanceTimesheetJob());
    }
}
