<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserEmployeeTypeMap;
use App\Models\EmpTypeMapWithEmpStatus;

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
        $currentDate = $date->format('Y-m-d');

        $all_user = User::all();

        foreach($all_user as $user){

            $dataa = UserEmployeeTypeMap::where('user_id', $user->id)->orderBy('id', 'DESC')->take(2)->get();

            if(count($dataa) == 1){

                if(empty($dataa[0]->to_date) && (strtotime($dataa[0]->from_date) <= strtotime($currentDate))){

                    $current_type = $dataa[0]->employee_type_id;
                    $current_type_map_id = $dataa[0]->id;
                    $current_type_from_date = $dataa[0]->from_date;
                }
                elseif((strtotime($dataa[0]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[0]->to_date) >= strtotime($currentDate)) && !empty($dataa[0]->to_date)){

                    $current_type = $dataa[0]->employee_type_id;
                    $current_type_map_id = $dataa[0]->id;
                    $current_type_from_date = $dataa[0]->from_date;
                }
                elseif((strtotime($dataa[0]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[0]->to_date) <= strtotime($currentDate)) && !empty($dataa[0]->to_date)){

                    $current_type = "Invalid";
                    $current_type_map_id = $dataa[0]->id;
                    $current_type_to_date = $dataa[0]->to_date;
                }
                else{
                    $current_type = "Invalid";
                    $current_type_map_id = $dataa[0]->id;
                    $current_type_to_date = $dataa[0]->to_date;
                }
            }
            else{
                if(empty($dataa[0]->to_date) && (strtotime($dataa[0]->from_date) <= strtotime($currentDate))){

                    $current_type = $dataa[0]->employee_type_id;
                    $current_type_map_id = $dataa[0]->id;
                    $current_type_from_date = $dataa[0]->from_date;
                }
                elseif((strtotime($dataa[0]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[0]->to_date) >= strtotime($currentDate)) && !empty($dataa[0]->to_date)){

                    $current_type = $dataa[0]->employee_type_id;
                    $current_type_map_id = $dataa[0]->id;
                    $current_type_from_date = $dataa[0]->from_date;
                }
                elseif((strtotime($dataa[0]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[0]->to_date) <= strtotime($currentDate)) && !empty($dataa[0]->to_date)){

                    $current_type = "Invalid";
                    $current_type_map_id = $dataa[0]->id;
                    $current_type_to_date = $dataa[0]->to_date;
                }
                elseif((strtotime($dataa[1]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[1]->to_date) >= strtotime($currentDate)) && !empty($dataa[1]->to_date)){

                    $current_type = $dataa[1]->employee_type_id;
                    $current_type_map_id = $dataa[1]->id;
                    $current_type_from_date = $dataa[1]->from_date;
                }
                else{
                    $current_type = "Invalid";
                    $current_type_map_id = 0;
                }
            }

            if($current_type != "Invalid"){

                if($currentDate == $current_type_from_date){

                    $chkingg = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $current_type_map_id)->orderBy('id', 'DESC')->first();

                    if(count($chkingg) > 0){
                       if($chkingg->employee_status_id != 1){

                            $sav = new EmpTypeMapWithEmpStatus;
                            $sav->user_emp_type_map_id = $current_type_map_id;
                            $sav->employee_status_id = 1;
                            $sav->from_date = $current_type_from_date;
                            $sav->remarks = "At the time emp type change.";
                            $sav->save(); 
                        } 
                    }
                    else{
                        $sav = new EmpTypeMapWithEmpStatus;
                        $sav->user_emp_type_map_id = $current_type_map_id;
                        $sav->employee_status_id = 1;
                        $sav->from_date = $current_type_from_date;
                        $sav->remarks = "At the time emp type change.";
                        $sav->save();
                    }

                    User::find($user->id)->update(['status' => 1]);
                }
                else{

                    $status = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $current_type_map_id)->get();

                    if(count($status) > 0){

                        foreach($status as $info){

                            if($currentDate == $info->from_date){

                                $stat = $info->employee_status_id;
                                
                                User::find($user->id)->update(['status' => $stat]);
                            }
                        }
                    }
                }                
            }
            else{

                $chkinggInvalid = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $current_type_map_id)->orderBy('id', 'DESC')->first();

                if(count($chkinggInvalid) > 0){

                    if((strtotime($chkinggInvalid->from_date) < strtotime($currentDate))) {

                        $chkinggInvalid->to_date = $current_type_to_date;
                        $chkinggInvalid->save();
                    }
                }

                User::find($user->id)->update(['status' => 10]);
            }
        }
    }
}
