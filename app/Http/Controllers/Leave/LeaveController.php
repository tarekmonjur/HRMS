<?php

namespace App\Http\Controllers\Leave;

use Auth;
use DB;
use App\Models\LeaveType;
use App\Models\Weekend;
use App\Models\Holiday;
use App\Models\User;
use App\Models\UserLeaveTypeMap;
use App\Models\EmployeeLeave;
use App\Models\WorkShiftEmployeeMap;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:hrms');
        $this->middleware('CheckPermissions', ['except' => ['changeStatus', 'getWeekendHolidays', 'view', 'details', 'chResponsibleStatus']]);

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
    }

    public function index(){

    	$data['title'] = "HRMS|Leave";
        $data['all_leave_types'] = LeaveType::where('leave_type_status', 1)->get();
        $data['leaves'] = EmployeeLeave::with('leaveType', 'userName.designation', 'responsibleUser', 'approvedByUser')->orderBy('employee_leave_status')->get();

    	return view('leave.leave', $data);
    }

    public function getWeekendHolidays($fromDate, $toDate, $userId){

        $fromDateAry = explode('-', $fromDate);
        $shiftDaysAry = [];
        $shiftDaysAry['days'] = [];
        $shiftDaysAry['weekends'] = [];
        $shiftWeekendsAry = [];

        $val = WorkShiftEmployeeMap::where('user_id', $userId)->whereYear('start_date', '=', $fromDateAry[0])->whereYear('end_date', '=', $fromDateAry[0])->get();

        foreach($val as $info){
            $shiftWeekendsAry = [];

            if($info->start_date == $info->end_date){
                $shiftDaysAry['days'][] = $info->start_date;

                $shift_temp = explode(',', $info->work_days);
                $shift_temp = array_map('trim', $shift_temp);
                $weekend_no = array_diff([1,2,3,4,5,6,7], $shift_temp);

                if(count($weekend_no)>0){
                    foreach($weekend_no as $w){
                        $shiftWeekendsAry[] = $this->return_day_from_number($w);
                    }
                }

                $shiftDaysAry['weekends'][] = implode(',', $shiftWeekendsAry);
            }
            else{

                $shift_temp = explode(',', $info->work_days);
                $shift_temp = array_map('trim', $shift_temp);
                $weekend_no = array_diff([1,2,3,4,5,6,7], $shift_temp);
                
                if(count($weekend_no)>0){
                    foreach($weekend_no as $w){
                        $shiftWeekendsAry[] = $this->return_day_from_number($w);
                    }
                }

                $day = 86400;  
                $format = 'Y-m-d';  
                $sTime = strtotime($info->start_date); 
                $eTime = strtotime($info->end_date); 
                $numDays = round(($eTime - $sTime) / $day) + 1;  
                $days = array();  

                for ($d = 0; $d < $numDays; $d++) {
                    $shiftDaysAry['days'][] = date($format, ($sTime + ($d * $day))); 
                    $shiftDaysAry['weekends'][] = implode(',', $shiftWeekendsAry);
                }     
            }
        }

        $initialApplyAry = [];

        if($fromDate == $toDate){
            $initialApplyAry[] = $fromDate;
        }
        else{
            $day = 86400;  
            $format = 'Y-m-d';  
            $sTime = strtotime($fromDate); 
            $eTime = strtotime($toDate); 
            $numDays = round(($eTime - $sTime) / $day) + 1;  
            $days = array();  

            for ($d = 0; $d < $numDays; $d++) {
                $initialApplyAry[] = date($format, ($sTime + ($d * $day))); 
            } 
        }

        $weekendCounter = 0;
        $holidayCounter = 0;
        
        //Inser days into array
        $forNormalWeekend = array_diff($initialApplyAry, $shiftDaysAry['days']); 
        $weekendFromShift = array_intersect($initialApplyAry, $shiftDaysAry['days']);

        //common codes ====
        $holidays_info = Holiday::whereBetween('holiday_from', ["$fromDateAry[0]-1-1", "$fromDateAry[0]-12-31"])->where('holiday_status', 1)->get();

        $allHolidayAry = [];

        foreach($holidays_info as $info){
            if($info->holiday_from == $info->holiday_to){
                $allHolidayAry[] = $info->holiday_from;
            }
            else{
                $day = 86400; 
                $format = 'Y-m-d'; 
                $sTime = strtotime($info->holiday_from);
                $eTime = strtotime($info->holiday_to); 
                $numDays = round(($eTime - $sTime) / $day) + 1;  
                $days = array();  

                for ($d = 0; $d < $numDays; $d++) {  
                    $allHolidayAry[] = date($format, ($sTime + ($d * $day)));
                } 
            }
        }

        //find common days b2in all holiday with shift
        $holidayWithShift = array_intersect($allHolidayAry, $weekendFromShift);
        //find common days b2in holiday with days not in assign shift
        $holidayWithNormalWeekend = array_intersect($allHolidayAry, $forNormalWeekend);

        //=================

        // ****************** Weekend from SHIFT ***********************
        
        $weekendNormalInShift = [];

        if(count($weekendFromShift) > 0){
            $holidayAry = [];

            foreach($weekendFromShift as $info){
                
                $index = array_search($info,$shiftDaysAry['days']);

                //check weekend
                $shiftWeekendDays = date("l", strtotime($info));
                $weekends_for_shift = explode(',', $shiftDaysAry['weekends'][$index]); 
                $weekends_for_shift = array_map('trim', $weekends_for_shift); // triming

                if(in_array($shiftWeekendDays, $weekends_for_shift)){
                    $weekendCounter++;
                }

                //check Holiday with out Weekend
                if(in_array($info, $holidayWithShift)){
                    if(!in_array($shiftWeekendDays, $weekends_for_shift)){
                        $holidayCounter++;
                    }
                }
            }

            if(count($forNormalWeekend) > 0){

                $fetchAllWeekends = Weekend::whereBetween('weekend_from', ["$fromDateAry[0]-1-1", "$fromDateAry[0]-12-31"])->get();

                $normalWithDaysAry = [];

                foreach($forNormalWeekend as $normalInfo){
                    foreach($fetchAllWeekends as $info){
                        if(!empty($info->weekend_to)){
                            if(strtotime($normalInfo) >= strtotime($info->weekend_from) && strtotime($normalInfo) <= strtotime($info->weekend_to))
                            {
                                $normalWithDaysAry[$normalInfo]['weekends'] = $info->weekend;
                            }
                        }
                        else{
                            if(strtotime($normalInfo) >= strtotime($info->weekend_from)){
                                $normalWithDaysAry[$normalInfo]['weekends'] = $info->weekend;
                            }
                        }
                    }
                }

                foreach($normalWithDaysAry as $key => $info){

                    // echo $key."---".$info['weekends']."<br>";
                    $dayName2 = date("l", strtotime($key));
                    $daysArray2 = array_map('trim', explode(",", $info['weekends']));
                    
                    if(!in_array($dayName2, $daysArray2)){
                        
                        $normalInShift[] = $key;
                    }else{
                        //responsible to return weeked number
                        $weekendNormalInShift[] = $key;
                    }
                }

                //find common days between weeked and holysays
                $conflictDays = array_intersect($weekendNormalInShift, $holidayWithNormalWeekend);

                $weekendCounter = $weekendCounter + count($weekendNormalInShift);
                $holidayCounter = $holidayCounter + (count($holidayWithNormalWeekend) - count($conflictDays));
            }

            $data['holidays'] = $holidayCounter;
            $data['weekend'] = $weekendCounter;
        }
        else{
            //***************** WEEKEND Calculation WITHOUT shift ******************

            $begin = new \DateTime( $fromDate );
            $end   = new \DateTime( $toDate );

            $sql = "SELECT ";
            $ii =0;
            for($i = $begin; $i <= $end; $i->modify('+1 day')){
                $dateCurrent = $i->format('Y-m-d');
              
                  if($ii > 0){
                        $sql .= ",";
                  }
                  $sql .= "(SELECT `weekend` FROM `weekends` WHERE ('".$dateCurrent."' BETWEEN `weekend_from` AND `weekend_to`)) '".$dateCurrent."'";
                  $ii++;
            }
             
            $testArray = json_decode(json_encode(DB::select( DB::raw($sql))), true);

            $filteredArrayOfficeDay = array();
            $filteredArrayWeekend = array();
            $normalInShift = [];
            $weekendNormalInShift = [];
            $holidayExistInRange = [];

            foreach ($testArray[0] as $date => $days) {
                $dayName = date("l", strtotime($date));
                $daysArray = array_map('trim', explode(",", $days));
                
                if(!in_array($dayName, $daysArray)){
                    
                    $filteredArrayOfficeDay[] = $date;
                }else{
                    //array for count weekend
                    $filteredArrayWeekend[] = $date;
                }
            }

            foreach($allHolidayAry as $info){
                if(strtotime($info) >= strtotime($fromDate) && strtotime($info) <= strtotime($toDate)){
                    $holidayExistInRange[] = $info;
                }
            }

            //find common days between weeked and holysays
            $conflictDays = array_intersect($filteredArrayWeekend, $holidayExistInRange);

            $data['weekend'] = count($filteredArrayWeekend);
            $data['holidays'] = count($holidayExistInRange) - count($conflictDays);
        }

        return $data;        
    }

    // public function userTakenLeave($id){

    //     $currentYear = date('Y');
    //     $data_map = UserLeaveTypeMap::with('leaveType')->where('user_id', $id)
    //                 ->where('active_from_year', '<=', $currentYear)
    //                 ->where('active_to_year', '>=', $currentYear)->where('status', 1)->get();

    //     $leave_amount_ary = [];   //leave total amount

    //     $sl = 0;
    //     foreach($data_map as $mapInfo){
    //         $leave_amount_ary[$sl]['id'] = $mapInfo->leave_type_id;
    //         $leave_amount_ary[$sl]['name'] = $mapInfo->leaveType->leave_type_name;
    //         $leave_amount_ary[$sl]['days'] = $mapInfo->number_of_days;
    //         $sl++;
    //     }

    //     $data_val = EmployeeLeave::with('leaveType')->where('user_id', $id)->whereIn('employee_leave_status', [1,2,3])->whereYear('employee_leave_from', $currentYear)->get();

    //     dd($data_val);

    //     $leave_type_id_ary = [];
    //     $leave_type_name_ary = [];
    //     $leave_type_days_ary = [];
    //     $taken_leave_ary = [];

    //     //calculate which type leave taken how many days...
    //     foreach($data_val as $info){
            
    //         $diff_days = $info->employee_leave_total_days;

    //         if(!in_array($info->leave_type_id, $leave_type_id_ary)){
    //             $leave_type_id_ary[] = $info->leave_type_id;
    //             $leave_type_name_ary[] = $info->leaveType->leave_type_name;
    //             $leave_type_days_ary[] = $diff_days;
    //         }
    //         else{
    //             $location = array_search($info->leave_type_id, $leave_type_id_ary);
    //             $old_days = $leave_type_days_ary[$location];
    //             $leave_type_days_ary[$location] = $diff_days + $old_days;
    //         }
    //     }

    //     if(count($leave_type_id_ary) > 0){
    //         for($i=0 ; $i<count($leave_type_id_ary) ; $i++){
    //             $taken_leave_ary[$i]['id'] = $leave_type_id_ary[$i];
    //             $taken_leave_ary[$i]['name'] = $leave_type_name_ary[$i];
    //             $taken_leave_ary[$i]['days'] = $leave_type_days_ary[$i];
    //         }
    //     }

    //     //combined leave amount and taken leave to show leave history
    //     $leave_amount_taken_combination = [];
    //     $sl = 0;
    //     foreach($leave_amount_ary as $info){
    //         $leave_amount_taken_combination[$sl]['id'] = $info['id'];
    //         $leave_amount_taken_combination[$sl]['name'] = $info['name'];
    //         $leave_amount_taken_combination[$sl]['amount_days'] = $info['days'];
            
    //         if(in_array($info['id'], $leave_type_id_ary)){
    //             $locId = array_search($info['id'], $leave_type_id_ary);
    //             $leave_amount_taken_combination[$sl]['taken_days'] = $taken_leave_ary[$locId]['days'];
    //             $leave_amount_taken_combination[$sl]['balance'] = $info['days'] - $taken_leave_ary[$locId]['days'];
    //         }
    //         else{
    //             $leave_amount_taken_combination[$sl]['taken_days'] = 0;
    //             $leave_amount_taken_combination[$sl]['balance'] = $info['days'];
    //         }

    //         $sl++;
    //     }

    //     //calculate remain leave to show leave type
    //     $leave_type_ary = [];
    //     $aryIndex = 0;
    //     foreach($leave_amount_ary as $amountInfo){

    //         if(in_array($amountInfo['id'], $leave_type_id_ary)){
    //             //find leave taken days using id from taken leave ary days
    //             $indexId = array_search($amountInfo['id'], $leave_type_id_ary);
    //             $leave_type_ary[$aryIndex]['id'] =  $amountInfo['id'];
    //             $leave_type_ary[$aryIndex]['name'] =  $leave_type_name_ary[$indexId];
    //             $leave_type_ary[$aryIndex]['days'] =  $amountInfo['days']-$leave_type_days_ary[$indexId];
    //         }
    //         else{
    //             $leave_type_ary[$aryIndex]['id'] =  $amountInfo['id'];
    //             $leave_type_ary[$aryIndex]['name'] =  $amountInfo['name'];
    //             $leave_type_ary[$aryIndex]['days'] =  $amountInfo['days'];
    //         }

    //         $aryIndex++;
    //     }
        
    //     $data['userHaveLeavs'] = $data_map;
    //     $data['taken_leave_type_id'] = $leave_type_id_ary;
    //     $data['taken_leave_type_name'] = $leave_type_name_ary;
    //     $data['taken_leave_type_days'] = $leave_type_days_ary;
    //     $data['taken_leave_ary'] = $taken_leave_ary; //leave taken
    //     $data['user_leave_type'] = $leave_type_ary; //leave remain
    //     $data['show_history'] = $leave_amount_taken_combination;

    //     session()->put('global_leave_type_ary', $leave_type_ary);

    //     return $data;
    // }

    public function create(Request $request){

        $this->validate($request, [
            'emp_name' => 'required',
            'emp_leave_type' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'leave_reason' => 'required',
            'leave_half_or_full' => 'required',
        ],[
            'emp_name.required' => 'Employee name is required.',
            'emp_leave_type.required' => 'Leave type is required.',
            'from_date.required' => 'Select date(from date) is required.',
            'to_date.required' => 'Select date(to date) is required.',
            'leave_half_or_full.required' => 'Leave status required.',
        ]);

        $emp_name = $request->emp_name;
        $emp_leave_type = $request->emp_leave_type;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $leave_reason = $request->leave_reason;
        $leave_contact_address = $request->leave_contact_address;
        $leave_contact_number = $request->leave_contact_number;
        $employee_leave_noc_required = $request->employee_leave_noc_required;
        $passport_no = $request->passport_no;
        $responsible_emp = $request->responsible_emp;
        $leave_half_or_full = $request->leave_half_or_full;

        $date1Timestamp = strtotime($from_date);
        $date2Timestamp = strtotime($to_date);
        $difference = $date2Timestamp - $date1Timestamp;
        $diff_days = floor($difference / (60*60*24) )+1;

        if($date2Timestamp < $date1Timestamp){
            //Error return if To date grater the from date
            
            $data['title'] = 'error';
            $data['message'] = "* Invalid date entry.";
            return $data;
        }

        $weekend_holiday_info = $this->getWeekendHolidays($from_date, $to_date, $emp_name);
        $leave_type_info = LeaveType::find($emp_leave_type);
        $chk_include_holiday = $leave_type_info->leave_type_include_holiday;

        $leave_type_ary = session()->get('global_leave_type_ary');

        if($chk_include_holiday != 1){
            $diff_days = $diff_days - ($weekend_holiday_info['holidays'] + $weekend_holiday_info['weekend']);
        }

        //check half day or full day start
        if($leave_half_or_full == 2){
            //half day leave
            if($diff_days > 1){
                $data['title'] = 'error';
                $data['message'] = "* For half day leave applicable for only one day.";

                return $data;
            }else{
                $diff_days = 0.50;
            }
        }

        //half day full day end

        $chkPending = EmployeeLeave::where('user_id', $emp_name)->whereIn('employee_leave_status', [1, 2])->get();

        if(count($chkPending) > 0){
            $data['title'] = 'error';
            $data['message'] = "* Already one leave application is pending/forward.";
        }
        else{
            foreach($leave_type_ary as $info){
                if($emp_leave_type == $info['id']){
                    $chk = $info['days'] - $diff_days;

                    if(empty($info['days']) || $info['days'] < 0){
                        //days are undefined
                        //that means no limit
                        $chk = 1;
                    }

                    if($chk >= 0){
                        
                        $supervisor_id = User::find($emp_name)->supervisor_id;
                        
                        $file_name = '';
                        if(request()->hasFile('file')){
            
                            $file = request()->file('file');
                            $exten = $file->extension();
                            $temp_name = date("Ymd_His");
                            $folder = "/leave_doc/$emp_name";

                            //storage/app/public
                            $file_name = $temp_name.".".$exten;

                            request()->file('file')->storeAs($folder, $file_name);
                        }
                        
                        try{
                            $sav = new EmployeeLeave;
                            $sav->user_id = $emp_name;
                            $sav->leave_type_id = $emp_leave_type; 
                            $sav->employee_leave_from = $from_date; 
                            $sav->employee_leave_to = $to_date;
                            $sav->employee_leave_total_days = $diff_days;
                            $sav->employee_leave_user_remarks = $leave_reason;
                            $sav->employee_leave_half_or_full = $leave_half_or_full;
                            $sav->employee_leave_contact_address = $leave_contact_address;
                            $sav->employee_leave_contact_number = $leave_contact_number;
                            $sav->employee_leave_noc_required = $employee_leave_noc_required;
                            $sav->employee_leave_passport_no = $passport_no;
                            $sav->employee_leave_responsible_person = $responsible_emp;
                            $sav->employee_leave_attachment = $file_name;
                            $sav->employee_leave_supervisor = $supervisor_id;
                            $sav->employee_leave_status = 1;
                            $sav->save();
                        
                            $data['title'] = 'success';
                            $data['message'] = 'data successfully added!';

                        }catch (\Exception $e) {
                            
                           $data['title'] = 'error';
                           $data['message'] = 'data not added!';
                        }
                    }
                    else{
                        $data['title'] = 'error';
                        $data['message'] = "* You can only apply for ".$info['days']." days or below ".$info['days']." days leave.";
                    }
                }
            }
        }
            
        return $data;
    }

    public function edit($id){

        $value = EmployeeLeave::find($id); 
        $data['hdn_id'] = $id;
        $data['user_id'] = $value->user_id;
        $data['leave_type_id'] = $value->leave_type_id; 
        $data['employee_leave_from'] = $value->employee_leave_from; 
        $data['employee_leave_to'] = $value->employee_leave_to;
        $data['employee_leave_total_days'] = $value->employee_leave_total_days;
        $data['employee_leave_user_remarks'] = $value->employee_leave_user_remarks;
        $data['employee_leave_half_or_full'] = $value->employee_leave_half_or_full;
        $data['employee_leave_contact_address'] = $value->employee_leave_contact_address;
        $data['employee_leave_contact_number'] = $value->employee_leave_contact_number;
        $data['employee_leave_noc_required'] = $value->employee_leave_noc_required;
        $data['employee_leave_passport_no'] = $value->employee_leave_passport_no;
        $data['employee_leave_responsible_person'] = $value->employee_leave_responsible_person;
        $data['employee_leave_attachment'] = $value->employee_leave_attachment;
        $data['employee_leave_supervisor'] = $value->employee_leave_supervisor;
        $data['employee_leave_status'] = $value->employee_leave_status;
        $data['employee_leave_recommend_to'] = $value->employee_leave_recommend_to;

        $currentYear = date('Y');
        $data_map = UserLeaveTypeMap::with('leaveType')->where('user_id', $value->user_id)
                    ->where('active_from_year', '<=', $currentYear)
                    ->where('active_to_year', '>=', $currentYear)->where('status', 1)->get();

        $leave_amount_ary = [];   //leave total amount

        $sl = 0;
        foreach($data_map as $mapInfo){
            $leave_amount_ary[$sl]['id'] = $mapInfo->leave_type_id;
            $leave_amount_ary[$sl]['name'] = $mapInfo->leaveType->leave_type_name;
            $leave_amount_ary[$sl]['days'] = $mapInfo->number_of_days;
            $sl++;
        }

        $data_val = EmployeeLeave::with('leaveType')->where('user_id', $value->user_id)->whereIn('employee_leave_status', [1,2,3])->get();
        $leave_type_id_ary = [];
        $leave_type_name_ary = [];
        $leave_type_days_ary = [];
        $taken_leave_ary = [];

        //calculate which type leave taken how many days...
        foreach($data_val as $info){
            
            $diff_days = $info->employee_leave_total_days;

            if(!in_array($info->leave_type_id, $leave_type_id_ary)){
                $leave_type_id_ary[] = $info->leave_type_id;
                $leave_type_name_ary[] = $info->leaveType->leave_type_name;
                $leave_type_days_ary[] = $diff_days;
            }
            else{
                $location = array_search($info->leave_type_id, $leave_type_id_ary);
                $old_days = $leave_type_days_ary[$location];
                $leave_type_days_ary[$location] = $diff_days + $old_days;
            }
        }

        if(count($leave_type_id_ary) > 0){
            for($i=0 ; $i<count($leave_type_id_ary) ; $i++){
                $taken_leave_ary[$i]['id'] = $leave_type_id_ary[$i];
                $taken_leave_ary[$i]['name'] = $leave_type_name_ary[$i];
                $taken_leave_ary[$i]['days'] = $leave_type_days_ary[$i];
            }
        }

        //combined leave amount and taken leave to show leave history
        $leave_amount_taken_combination = [];
        $sl = 0;
        foreach($leave_amount_ary as $info){
            $leave_amount_taken_combination[$sl]['id'] = $info['id'];
            $leave_amount_taken_combination[$sl]['name'] = $info['name'];
            $leave_amount_taken_combination[$sl]['amount_days'] = $info['days'];
            
            if(in_array($info['id'], $leave_type_id_ary)){
                $locId = array_search($info['id'], $leave_type_id_ary);
                $leave_amount_taken_combination[$sl]['taken_days'] = $taken_leave_ary[$locId]['days'];
                $balanceLeave = $info['days'] - $taken_leave_ary[$locId]['days'];
                $leave_amount_taken_combination[$sl]['balance'] = $balanceLeave<0 ? 0 : $balanceLeave;
            }
            else{
                $leave_amount_taken_combination[$sl]['taken_days'] = 0;
                $leave_amount_taken_combination[$sl]['balance'] = $info['days']<0 ? 0 : $info['days'];
            }

            $sl++;
        }

        //calculate remain leave to show leave type
        $leave_type_ary = [];
        $aryIndex = 0;
        foreach($leave_amount_ary as $amountInfo){

            if(in_array($amountInfo['id'], $leave_type_id_ary)){
                //find leave taken days using id from taken leave ary days
                $indexId = array_search($amountInfo['id'], $leave_type_id_ary);
                $leave_type_ary[$aryIndex]['id'] =  $amountInfo['id'];
                $leave_type_ary[$aryIndex]['name'] =  $leave_type_name_ary[$indexId];
                $leave_type_ary[$aryIndex]['days'] =  $amountInfo['days']-$leave_type_days_ary[$indexId];
            }
            else{
                $leave_type_ary[$aryIndex]['id'] =  $amountInfo['id'];
                $leave_type_ary[$aryIndex]['name'] =  $amountInfo['name'];
                $leave_type_ary[$aryIndex]['days'] =  $amountInfo['days'];
            }

            $aryIndex++;
        }

        //only for edit ... start
        $sl = 0;
        foreach($leave_type_ary as $eInfo){
            
            if($eInfo['id'] == $data['leave_type_id']){
                break;
            }else{
                $sl++;  
            }
        }    

            //add old total days with this
        $leave_type_ary[$sl]['days'] = $leave_type_ary[$sl]['days'] + $data['employee_leave_total_days'];
        
        //only for edit ... end

        $data['userHaveLeavs'] = $data_map;
        $data['taken_leave_type_id'] = $leave_type_id_ary;
        $data['taken_leave_type_name'] = $leave_type_name_ary;
        $data['taken_leave_type_days'] = $leave_type_days_ary;
        $data['taken_leave_ary'] = $taken_leave_ary; //leave taken
        $data['user_leave_type'] = $leave_type_ary; //leave remain
        $data['show_history'] = $leave_amount_taken_combination;

        session()->put('global_leave_type_ary', $leave_type_ary);

        return $data;
    }

    public function details($emp_no=null){

        if(!empty($emp_no)){
            $user_val = User::where('employee_no', $emp_no)->first();
            $user_id = $user_val->id;
        }else{
            $user_id = Auth::user()->id;
        }

        $data['title'] = "HRMS|Leave Details";
        $data['user_id'] = $user_id;
        $data['all_leave_types'] = LeaveType::where('leave_type_status', 1)->get();
        $data['leaves'] = EmployeeLeave::where('user_id', $user_id)->with('leaveType', 'userName.designation', 'responsibleUser', 'approvedByUser')->orderBy('employee_leave_status')->get();
        $data['forwards'] = EmployeeLeave::where('employee_leave_recommend_to', $user_id)->with('leaveType', 'userName.designation', 'responsibleUser', 'approvedByUser')->orderBy('employee_leave_status')->get();
        $data['supervisors'] = EmployeeLeave::where('employee_leave_supervisor', $user_id)->with('leaveType', 'userName.designation', 'responsibleUser', 'approvedByUser')->orderBy('employee_leave_status')->get();
        $data['responsibles'] = EmployeeLeave::where('employee_leave_responsible_person', $user_id)->with('leaveType', 'userName.designation', 'responsibleUser', 'approvedByUser', 'responsibleUserStatApprove')->orderBy('employee_leave_status')->get();

        return view('leave.details', $data);
    }

    public function update(Request $request){

        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $current_date = $date->format('Y-m-d'); 

        $this->validate($request, [
            'hdn_id' => 'required',
            'edit_leave_type_id' => 'required',
            'edit_from_date' => 'required',
            'edit_to_date' => 'required',
            'edit_leave_reason' => 'required',
            'want_to_forward' => 'nullable',
            'edit_forward_to' => 'required_if:want_to_forward,1',
            'edit_leave_half_or_full' => 'required',
        ],[
            'edit_leave_type_id.required' => 'Leave type is required.',
            'edit_from_date.required' => 'Select date(from date) is required.',
            'edit_to_date.required' => 'Select date(to date) is required.',
            'edit_leave_reason.required' => 'Leave reason field is required.',
            'edit_leave_half_or_full.required' => 'Leave status required.',
            'edit_forward_to.required_if' => 'Select employee for forward.',
        ]);

        $emp_user_id = EmployeeLeave::find($request->hdn_id)->user_id;

        $emp_leave_type = $request->edit_leave_type_id;
        $from_date = $request->edit_from_date;
        $to_date = $request->edit_to_date;
        $leave_reason = $request->edit_leave_reason;
        $leave_contact_address = $request->edit_leave_contact_address;
        $leave_contact_number = $request->edit_leave_contact_number;
        $employee_leave_noc_required = $request->edit_employee_leave_noc_required;
        $passport_no = $request->edit_passport_no;
        $responsible_emp = $request->edit_responsible_emp;
        $leave_half_or_full = $request->edit_leave_half_or_full;

        if($request->want_to_forward > 0){
            $forward_to = $request->edit_forward_to;
        }
        else{
            $forward_to = '';
        }

        //check .... 
        $weekend_holiday_info = $this->getWeekendHolidays($from_date, $to_date, $emp_user_id);
        $leave_type_info = LeaveType::find($emp_leave_type);
        $chk_include_holiday = $leave_type_info->leave_type_include_holiday;

        $leave_type_ary = session()->get('global_leave_type_ary');

        $date1Timestamp = strtotime($from_date);
        $date2Timestamp = strtotime($to_date);
        $difference = $date2Timestamp - $date1Timestamp;
        $diff_days = floor($difference / (60*60*24) )+1;

        if($chk_include_holiday != 1){
            $diff_days = $diff_days - ($weekend_holiday_info['holidays'] + $weekend_holiday_info['weekend']);
        }

        //check half day or full day start
        if($leave_half_or_full == 2){
            //half day leave
            if($diff_days > 1){
                $data['title'] = 'error';
                $data['message'] = "* For half day leave applicable for only one day.";

                return $data;
            }else{
                $diff_days = 0.50;
            }
        }

        //half day full day end

        if($date2Timestamp >= $date1Timestamp){
            foreach($leave_type_ary as $info){
                if($emp_leave_type == $info['id']){
                    $chk = $info['days'] - $diff_days;

                    if(empty($info['days']) || $info['days'] < 0){
                        //days are undefined
                        //that means no limit
                        $chk = 1;
                    }

                    if($chk >= 0){

                        $file_name = '';
                        if(request()->hasFile('file')){
            
                            //delete old file
                            $oldFileInfo = EmployeeLeave::find($request->hdn_id);

                            $file = request()->file('file');
                            $exten = $file->extension();
                            $temp_name = date("Ymd_His");
                            $folder = "/leave_doc/$oldFileInfo->user_id";

                            //storage/app/public
                            $file_name = $temp_name.".".$exten;
                            request()->file('file')->storeAs($folder, $file_name);
                            
                            if(!empty($oldFileInfo->employee_leave_attachment)){
                                $old_file_folder = "/leave_doc/".$oldFileInfo->user_id."/".$oldFileInfo->employee_leave_attachment;
                                if(Storage::exists($old_file_folder)){
                                    Storage::delete($old_file_folder);
                                }
                            }
                        }
                        
                        try{
                            $update_ary = [
                                'leave_type_id' => $emp_leave_type, 
                                'employee_leave_from' => $from_date, 
                                'employee_leave_to' => $to_date,
                                'employee_leave_total_days' => $diff_days,
                                'employee_leave_user_remarks' => $leave_reason,
                                'employee_leave_half_or_full' => $leave_half_or_full,
                                'employee_leave_contact_address' => $leave_contact_address,
                                'employee_leave_contact_number' => $leave_contact_number,
                                'employee_leave_noc_required' => $employee_leave_noc_required,
                                'employee_leave_passport_no' => $passport_no,
                                'employee_leave_responsible_person' => $responsible_emp,
                                'employee_leave_attachment' => $file_name,
                            ];
                             if(!empty($forward_to)){
                                $update_ary['employee_leave_recommend_to'] = $forward_to;
                                $update_ary['employee_leave_status'] = 2;
                            }

                            EmployeeLeave::where('id',$request->hdn_id)->update($update_ary);
                        
                            $data['title'] = 'success';
                            $data['message'] = 'data successfully added!';

                        }catch (\Exception $e) {
                            
                           $data['title'] = 'error';
                           $data['message'] = 'data not added!';
                        }
                    }
                    else{
                        $data['title'] = 'error';
                        $data['message'] = "* You can only apply for ".$info['days']." days or below ".$info['days']." days leave.";
                    }
                }
            }
        }
        else{
            $data['title'] = 'error';
            $data['message'] = "* Invalid date entry.";
        }
        //end check


        return response()->json($data);
    }

    public function changeStatus($id, $stat){

        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));

        $val = EmployeeLeave::find($id);
        $val->employee_leave_status = $stat;
        $val->employee_leave_approved_by = Auth::user()->id; 
        //approval_date use for only both Approve or Cancel
        $val->employee_leaves_approval_date = $date->format('Y-m-d'); 
        $val->save();
    }

    public function chResponsibleStatus($id, $stat, $loginEmp){

        $val = EmployeeLeave::find($id);
        $val->employee_leave_responsible_person_status = $stat;
        $val->employee_leave_responsible_person_status_change_by = Auth::user()->id;
        $val->save();
    }


    public function return_day_from_number($num){
        
        if($num == 1){
            $val = "Saturday";
        }
        elseif($num == 2){
            $val = "Sunday";
        }
        elseif($num == 3){
            $val = "Monday";
        }
        elseif($num == 4){
            $val = "Tuesday";
        }
        elseif($num == 5){
            $val = "Wednesday";
        }
        elseif($num == 6){
            $val = "Thursday";
        }
        elseif($num == 7){
            $val = "Friday";
        }

        return $val;
    }
}
