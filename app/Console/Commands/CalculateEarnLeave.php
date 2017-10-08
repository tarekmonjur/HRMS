<?php

namespace App\Console\Commands;

use App\Jobs\CalculateEarnLeaveJob;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Setup\Config;

class CalculateEarnLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'calculate:earnLeave';
    protected $signature = 'calculate:earnLeave {dbname}?';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Earn Leave Values';

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
        // \Artisan::call("db:connect", ['database' => '1500978046_TestClone01']);
        \Artisan::call("db:connect", ['database' => $this->argument('dbname')]);

        dispatch(new CalculateEarnLeaveJob());
    }
}

