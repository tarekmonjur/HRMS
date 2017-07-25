<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;
use App\Models\UserLeaveTypeMap;
use App\Models\EmployeeDetail;
use App\Models\UserEmployeeTypeMap;
use App\Models\Setup\Config;
use Session;

class CalculateEarnLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:earnLeave';
    // protected $signature = 'calculate:earnLeave {dbname}';

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
        // \Artisan::call("db:connect", ['database' => '1497516153_ALl_new_menu']);
        // \Artisan::call("db:connect", ['database' => $this->argument('dbname')]);

        $all = Config::all();

        foreach($all as $info){
                
                $dbName = $info->database_name;
                \Artisan::call("db:connect", ['database' => $dbName]);

                $currentYear = date('Y');
                $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
                $current_date = $date->format('Y-m-d'); 

                $find_earn_leave = LeaveType::where('leave_type_is_earn_leave', 1)
                                    ->where('leave_type_active_from_year', '<=', $currentYear)
                                    ->where('leave_type_active_to_year', '>=', $currentYear)
                                    ->where('leave_type_status', 1)->first();
            
                if(count($find_earn_leave) > 0){
                    $earn_leave_id = $find_earn_leave->id;
                    $valid_after_month = $find_earn_leave->leave_type_valid_after_months;
                    $number_of_days = $find_earn_leave->leave_type_number_of_days;
                    $days_to_increase = round(365/$number_of_days);

                    $users_with_earn_leave = UserLeaveTypeMap::where('leave_type_id', $earn_leave_id)->get();

                    if(count($users_with_earn_leave) > 0){
                        foreach($users_with_earn_leave as $info){
                        
                            $empDetails = UserEmployeeTypeMap::where('user_id', $info->user_id)
                            ->whereNotNull('from_date')->first();

                            if(count($empDetails) > 0){

                                if(!empty($info->earn_leave_upgrade_date)){
                                    $now = strtotime($current_date);
                                    $prev_date = strtotime($info->earn_leave_upgrade_date);
                                    $datediff = $now - $prev_date;
                                    $dateBtween = floor($datediff / (60 * 60 * 24));
                                    $startCalDate = $info->earn_leave_upgrade_date;
                                }else{
                                    $now = strtotime($current_date);
                                    $prev_date = strtotime($empDetails->from_date);
                                    $datediff = $now - $prev_date;
                                    $dateBtween = floor($datediff / (60 * 60 * 24));
                                    $startCalDate = $empDetails->from_date;
                                }

                                $earnLeaves = floor($dateBtween/$days_to_increase);
                                $calTillDays = ($days_to_increase * $earnLeaves);

                                if($earnLeaves > 0){
                                    $date = strtotime("+".$calTillDays." days", strtotime($startCalDate));
                                    $earn_leave_upgrade_date =  date("Y-m-d", $date);

                                    $users_with_earn_leave = UserLeaveTypeMap::where('leave_type_id', $earn_leave_id)->where('user_id', $info->user_id)->first();

                                    $sum_earn_leave_amount = ($users_with_earn_leave->number_of_days>=0?$users_with_earn_leave->number_of_days:0) + $earnLeaves;

                                    //update UserLeaveTypeMap Table
                                    UserLeaveTypeMap::where('id', $users_with_earn_leave->id)->update(['number_of_days' => $sum_earn_leave_amount, 'earn_leave_upgrade_date' => $earn_leave_upgrade_date]);
                                }
                            }
                        }
                    }
                }
        }


                
    }
}
