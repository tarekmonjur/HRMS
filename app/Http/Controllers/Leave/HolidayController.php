<?php

namespace App\Http\Controllers\Leave;

use Auth;
use DB;
use App\Models\Holiday;
use App\Models\EmployeeLeave;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HolidayController extends Controller
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

    	$data['title'] = "HRMS-Holiday";
    	return view('leave.holidays', $data);
    }

    public function getAllData(){

        return Holiday::orderBy('holiday_from', 'DESC')->get();
    }

    public function create(Request $request){

        $prevDate = date('Y-m-d', strtotime($request->from_date .' -1 day'));

		$this->validate($request, [
            'holiday_name' => 'required',
            'from_date' => 'required',
            'to_date' => 'required|after:'.$prevDate,
            'holiday_status' => 'required',
        ]);

        $from = $request->from_date;
        $to = $request->to_date;

        $chkAlreadyExist = DB::select( DB::raw("SELECT * FROM `holidays` where (`holiday_from` <= '$from' and `holiday_to` >= '$to') or (`holiday_from` <= '$from' and `holiday_to` >= '$from') or (`holiday_from` <= '$to' and `holiday_to` >= '$to') or (`holiday_from` >= '$from' and `holiday_from` <= '$to')") );

        if(count($chkAlreadyExist) > 0){
            
            $data['title'] = 'error';
            $data['message'] = 'date already exist!';
            return response()->json($data);                 
        }

        DB::beginTransaction();

        try{
            $counter = 0;
            if($request->holiday_status == 1){

                $val = DB::select( DB::raw("SELECT employee_leaves.*, leave_types.* FROM `employee_leaves` JOIN leave_types ON employee_leaves.leave_type_id=leave_types.id where (`employee_leave_from` <= '$from' and `employee_leave_to` >= '$to') or (`employee_leave_from` <= '$from' and `employee_leave_to` >= '$from') or (`employee_leave_from` <= '$to' and `employee_leave_to` >= '$to') or (`employee_leave_from` >= '$from' and `employee_leave_from` <= '$to') or (`employee_leave_to` >= '$from' and `employee_leave_to` <= '$to')") );   

                if(count($val) > 0){
                    $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));

                    foreach($val as $info){
                        if($info->employee_leave_status != 4 && $info->leave_type_include_holiday == 0){
                            
                            $counter++;
                            // echo $info->id."**".$info->employee_leave_from."**".$info->employee_leave_to."**".$info->leave_type_name."**".$info->leave_type_include_holiday."<br/>";
                            $upd = EmployeeLeave::find($info->id);
                            $upd->employee_leave_status = 4;
                            $upd->employee_leave_approved_by = Auth::user()->id; 
                            $upd->employee_leaves_approval_date = $date->format('Y-m-d'); 
                            $upd->save();
                        }
                    }    
                }
            }

            if($counter > 0){
                $extra_msg = "SORRY! $counter Leave application canceled.";
            }else{
                $extra_msg = "No Leave application effected.";
            }
            
            Holiday::create([
                'holiday_name' => $request->holiday_name,
                'holiday_from' => $request->from_date, 
                'holiday_to' => $request->to_date, 
                'holiday_details' => $request->holiday_description,
                'holiday_status' => $request->holiday_status,
            ]);
        
            DB::commit();
            $data['title'] = 'success';
            $data['message'] = $extra_msg.' Data successfully added!';

        }catch (\Exception $e) {
           
           DB::rollback(); 
           $data['title'] = 'error';
           $data['message'] = 'data not added!';
        }

        return response()->json($data);    	
    }

    public function edit($id){

    	$value = Holiday::find($id);
    	$data['holiday_name'] = $value->holiday_name;
        $data['holiday_from'] = $value->holiday_from; 
        $data['holiday_to'] = $value->holiday_to; 
        $data['holiday_details'] = $value->holiday_details;
        $data['holiday_status'] = $value->holiday_status;
    	$data['hdn_id'] = $id;

    	return response()->json($data); 
    }

    public function update(Request $request){
        
        $prevDate = date('Y-m-d', strtotime($request->edit_from_date .' -1 day'));
        
        $this->validate($request, [
            'edit_holiday_name' => 'required',
            'edit_from_date' => 'required',
            'edit_to_date' => 'required|after:'.$prevDate,
            'edit_holiday_status' => 'required',
        ]);

        $hdn_id = $request->hdn_id;
        $from = $request->edit_from_date;
        $to = $request->edit_to_date;
    
        $chkAlreadyExist = DB::select( DB::raw("SELECT * FROM `holidays` where `id` != '$hdn_id' and ((`holiday_from` <= '$from' and `holiday_to` >= '$to') or (`holiday_from` <= '$from' and `holiday_to` >= '$from') or (`holiday_from` <= '$to' and `holiday_to` >= '$to') or (`holiday_from` >= '$from' and `holiday_from` <= '$to'))") );

        if(count($chkAlreadyExist) > 0){
            
            $data['title'] = 'error';
            $data['message'] = 'date already exist! update not possible.';
            return response()->json($data);                 
        }

        DB::beginTransaction();

        try{
            $counterUpdate = 0;

                $val_edit = DB::select( DB::raw("SELECT employee_leaves.id as empId, employee_leaves.*, leave_types.* FROM `employee_leaves` JOIN leave_types ON employee_leaves.leave_type_id=leave_types.id where (`employee_leave_from` <= '$from' and `employee_leave_to` >= '$to') or (`employee_leave_from` <= '$from' and `employee_leave_to` >= '$from') or (`employee_leave_from` <= '$to' and `employee_leave_to` >= '$to') or (`employee_leave_from` >= '$from' and `employee_leave_from` <= '$to') or (`employee_leave_to` >= '$from' and `employee_leave_to` <= '$to')") );

                if(count($val_edit) > 0){
                    $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));

                    foreach($val_edit as $info){
                        if($info->employee_leave_status != 4 && $info->leave_type_include_holiday == 0){
                            
                            $counterUpdate++;
                            // echo $info->empId."**stat**".$info->employee_leave_status."**".$info->employee_leave_from."**".$info->employee_leave_to."**".$info->leave_type_name."**".$info->leave_type_include_holiday."<br/>";
                            $upd = EmployeeLeave::find($info->empId);
                            $upd->employee_leave_status = 4;
                            $upd->employee_leave_approved_by = Auth::user()->id; 
                            $upd->employee_leaves_approval_date = $date->format('Y-m-d'); 
                            $upd->save();
                        }
                    }    
                }

            if($counterUpdate > 0){
                $extra_msg = "SORRY! $counterUpdate Leave application canceled.";
            }else{
                $extra_msg = "No Leave application effected.";
            }

            $data['data'] = Holiday::where('id',$request->hdn_id)->update([
                'holiday_name' => $request->edit_holiday_name,
                'holiday_from' => $request->edit_from_date, 
                'holiday_to' => $request->edit_to_date, 
                'holiday_details' => $request->edit_holiday_description,
                'holiday_status' => $request->edit_holiday_status,
            ]);
        
            DB::commit();
            $data['title'] = 'success';
            $data['message'] = $extra_msg.'Data successfully updated!';

        }catch (\Exception $e) {
            
            DB::rollback();
            $data['title'] = 'error';
            $data['message'] = 'data not added!';
        }

        return response()->json($data);
    }
}
