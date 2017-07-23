<?php

namespace App\Http\Controllers\Leave;

use Auth;
use DB;
use App\Models\EmployeeType;
use App\Models\LeaveType;
use App\Models\UserLeaveTypeMap;
use App\Models\EmployeeDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaveTypeController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth:hrms');
        $this->middleware('CheckPermissions', ['except' => ['getAllData']]);

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
    }

    public function index(){

    	$data['title'] = "HRMS|Leave Type";
    	$data['emp_types'] = EmployeeType::where('status',1)->orderBy('id','DESC')->get();

    	return view('leave.leave_type', $data);
    }

    public function getAllData(){

        return LeaveType::with('emp_types')->orderBy('id', 'DESC')->get();
    }

    public function create(Request $request){

		$this->validate($request, [
            'type_name' => 'required',
            'emp_type' => 'required',
            'from_year' => 'required',
            'to_year' => 'required',
        ],
        [
		    'emp_type.required' => 'Effective for is mendatory.',
		]);   

		$srt_emp_type = implode(', ', $request->emp_type);
		$is_remain = $request->carry_to_next_year > 0?1:0;
        $include_holiday = $request->include_holiday > 0?1:0;
        $is_earn = $request->is_earn > 0?1:0;
        $is_sellable = $request->sellable > 0?1:0;
		$leave_type_with_out_pay = $request->leave_type_with_out_pay > 0?1:0;

        try{
            $sav = new LeaveType;
            $sav->leave_type_name = $request->type_name;
            $sav->leave_type_number_of_days = $request->duration;
            $sav->leave_type_effective_for = $srt_emp_type; 
            $sav->leave_type_valid_after_months = $request->valid_after;
            $sav->leave_type_details = $request->type_details;
            $sav->leave_type_with_out_pay = $leave_type_with_out_pay;
            $sav->leave_type_is_earn_leave = $is_earn;
            $sav->leave_type_is_sellable = $is_sellable;
            $sav->leave_type_max_sell_limit = $request->max_sell_limit;
            $sav->leave_type_is_remain = $is_remain;
            $sav->leave_type_max_remain_limit = $request->max_remain_limit;
            $sav->leave_type_include_holiday = $include_holiday;
            $sav->leave_type_active_from_year = $request->from_year;
            $sav->leave_type_active_to_year = $request->to_year;
            $sav->leave_type_created_by = Auth::user()->id;
            $sav->leave_type_status = 1;
            $sav->save();
        
            $data['title'] = 'success';
            $data['message'] = 'data successfully added!';

        }catch (\Exception $e) {
            
           $data['title'] = 'error';
           $data['message'] = 'data not added!';
        }

        return response()->json($data);    	
    }

    public function edit($id){

    	$value = LeaveType::find($id);
        $data['leave_type_valid_after_months'] = $value->leave_type_valid_after_months;
        $data['leave_type_is_earn_leave'] = $value->leave_type_is_earn_leave;
        $data['leave_type_is_sellable'] = $value->leave_type_is_sellable;
        $data['leave_type_max_sell_limit'] = $value->leave_type_max_sell_limit;
        $data['leave_type_max_remain_limit'] = $value->leave_type_max_remain_limit;
    	$data['leave_type_name'] = $value->leave_type_name;
        $data['leave_type_number_of_days'] = $value->leave_type_number_of_days; 
        $data['leave_type_effective_for'] = $value->leave_type_effective_for; 
        $data['leave_type_details'] = $value->leave_type_details;
        $data['leave_type_with_out_pay'] = $value->leave_type_with_out_pay;
        $data['leave_type_is_remain'] = $value->leave_type_is_remain;
        $data['leave_type_include_holiday'] = $value->leave_type_include_holiday;
        $data['leave_type_active_from_year'] = $value->leave_type_active_from_year;
        $data['leave_type_active_to_year'] = $value->leave_type_active_to_year;
        $data['leave_type_status'] = $value->leave_type_status;
    	$data['hdn_id'] = $id;

    	return response()->json($data); 
    }

    public function update(Request $request){

        $this->validate($request, [
            'type_name' => 'required',
            'emp_type' => 'required',
            'from_year' => 'required',
            'to_year' => 'required',
            'hdn_id' => 'required',
        ],
        [
		    'emp_type.required' => 'Effective for is mendatory.',
		]); 

		$srt_emp_type = implode(', ', $request->emp_type);
		$is_remain = $request->carry_to_next_year > 0?1:0;
		$include_holiday = $request->include_holiday > 0?1:0;
        $is_earn = $request->is_earn > 0?1:0;
        $is_sellable = $request->sellable > 0?1:0;
        $leave_type_with_out_pay = $request->leave_type_with_out_pay > 0?1:0;

        DB::beginTransaction();

        try{
            $data['data'] = LeaveType::where('id',$request->hdn_id)->update([
                'leave_type_name' => $request->type_name,
                'leave_type_number_of_days' => $request->duration, 
                'leave_type_effective_for' => $srt_emp_type, 
                'leave_type_details' => $request->type_details,
                'leave_type_with_out_pay' => $leave_type_with_out_pay,
                'leave_type_is_earn_leave' => $is_earn,
                'leave_type_is_sellable' => $is_sellable,
                'leave_type_max_sell_limit' => $request->max_sell_limit,
                'leave_type_is_remain' => $is_remain,
                'leave_type_max_remain_limit' => $request->max_remain_limit,
                'leave_type_include_holiday' => $include_holiday,
                'leave_type_active_from_year' => $request->from_year,
                'leave_type_active_to_year' => $request->to_year,
                'leave_type_updated_by' => Auth::user()->id,
                'leave_type_status' => $request->leave_type_status,
            ]);

            UserLeaveTypeMap::where('leave_type_id', $request->hdn_id)->update([
                'status' => $request->leave_type_status,
            ]);

            DB::commit();
            $data['title'] = 'success';
            $data['message'] = 'data successfully updated!';

        }catch (\Exception $e) {

            DB::rollback();
            $data['title'] = 'error';
            $data['message'] = 'data not added!';
        }

        return response()->json($data);
    }

    public function calculateEarnLeave(){

        $currentYear = date('Y');
        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $current_date = $date->format('Y-m-d'); 

        $find_earn_leave = LeaveType::where('leave_type_is_earn_leave', 1)
                            ->where('leave_type_active_from_year', '<=', $currentYear)
                            ->where('leave_type_active_to_year', '>=', $currentYear)->first();
    
        if(count($find_earn_leave) > 0){
            $earn_leave_id = $find_earn_leave->id;
            $valid_after_month = $find_earn_leave->leave_type_valid_after_months;
            $number_of_days = $find_earn_leave->leave_type_number_of_days;
            $days_to_increase = round(365/$number_of_days);

            $users_with_earn_leave = UserLeaveTypeMap::where('leave_type_id', $earn_leave_id)->get();

            if(count($users_with_earn_leave) > 0){
                foreach($users_with_earn_leave as $info){
                
                    $empDetails = EmployeeDetail::where('user_id', $info->user_id)->whereNotNull('confirm_date')->first();

                    if(count($empDetails) > 0){
                        // echo $empDetails->user_id." ** # ** ";
                        // echo $info->number_of_days;
                        if(!empty($info->earn_leave_upgrade_date)){
                            $now = strtotime($current_date);
                            $prev_date = strtotime($info->earn_leave_upgrade_date);
                            $datediff = $now - $prev_date;
                            $dateBtween = floor($datediff / (60 * 60 * 24));
                            $startCalDate = $info->earn_leave_upgrade_date;
                        }else{
                            $now = strtotime($current_date);
                            $prev_date = strtotime($empDetails->confirm_date);
                            $datediff = $now - $prev_date;
                            $dateBtween = floor($datediff / (60 * 60 * 24));
                            $startCalDate = $empDetails->confirm_date;
                        }

                        echo $dateBtween."<br/>";

                        $earnLeaves = floor($dateBtween/$days_to_increase);
                        echo $earnLeaves."*-* ---";

                        $calTillDays = ($days_to_increase * $earnLeaves);
                        echo $calTillDays."*-*<br/>";

                        if($earnLeaves > 0){
                            $date = strtotime("+".$calTillDays." days", strtotime($startCalDate));
                            $earn_leave_upgrade_date =  date("Y-m-d", $date);
                            echo $earn_leave_upgrade_date;

                            $users_with_earn_leave = UserLeaveTypeMap::where('leave_type_id', $earn_leave_id)->where('user_id', $info->user_id)->first();
                            $sum_earn_leave_amount = ($users_with_earn_leave->number_of_days>=0?$users_with_earn_leave->number_of_days:0) + $earnLeaves;

                            echo "<br/>Sum :".$sum_earn_leave_amount;
                            //update UserLeaveTypeMap Table
                            UserLeaveTypeMap::where('id', $users_with_earn_leave->id)->update(['number_of_days' => $sum_earn_leave_amount, 'earn_leave_upgrade_date' => $earn_leave_upgrade_date]);
                        }
                        else{
                            echo "not update";
                        }
                    }
                }
            }
        }

    }
}
