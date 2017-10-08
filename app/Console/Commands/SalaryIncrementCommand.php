<?php

namespace App\Console\Commands;

use App\Jobs\SalaryIncrementJob;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class SalaryIncrementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:increment {dbname}?';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment basic salary by effective date';

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
        Artisan::call("db:connect", ['database' => $this->argument('dbname')]);

        dispatch(new SalaryIncrementJob());

        \Config::set('database.connections.mysql_hrms.strict',true);
        // echo \DB::connection()->getDatabaseName();exit;
       
    }




}
