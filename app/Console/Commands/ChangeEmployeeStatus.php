<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserEmployeeStatusMap;
use App\Models\User;

class ChangeEmployeeStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:employeeStatus  {dbname}?';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Depand on eff date change emp status';

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

        $data = UserEmployeeStatusMap::where('from_date', $current_date)->get();

        if(count($data) > 0){

            foreach($data as $info){

                $findUser = User::where('id', $info->user_id)->first();
                $findUser->status = $info->employee_status_id;
                $findUser->save();
            }
        }
    }
}
