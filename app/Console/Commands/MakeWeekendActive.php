<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Weekend;

class MakeWeekendActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'active:weekend  {dbname}?';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Weekend Active';

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
        \Artisan::call("db:connect", ['database' => $this->argument('dbname')]);

        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $current_date = $date->format('Y-m-d');

        $chk = Weekend::where('weekend_from', $current_date)->first();

        if(count($chk) > 0){
            
            Weekend::where('status', 1)->update(['status' => 2]);
            Weekend::where('weekend_from', $current_date)->update(['status' => 1]);
        }
    }
}
