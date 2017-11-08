<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AttendanceTimesheet extends Model
{

    protected $fillable = ['user_id','date','observation','in_time','out_time','total_work_hour','late_count_time','late_hour','leave_type','created_at'];


    public function setInTimeAttribute($value){
        return $this->attributes['in_time'] = date('H:i',strtotime($value));
    }


    public function setOutTimeAttribute($value){
        return $this->attributes['out_time'] = date('H:i',strtotime($value));
    }
    

    public function getInTimeAttribute($value){
    	return ($value != "00:00:00")?date('h:i A',strtotime($value)):'00:00:00';
    }


    public function getOutTimeAttribute($value){
    	return ($value != "00:00:00")?date('h:i A',strtotime($value)):'00:00:00';
    }


    public function getTotalWorkHourAttribute($value){
        if(!empty($value) && $value !=Null){
            $dt = Carbon::parse($value);
            $time = '';
            $time .= ($dt->hour)?$dt->hour.' hour':'0 hour';
            $time .= ($dt->minute)?', '.$dt->minute.' minute':', 0 minute';
            return $time;
        }else{
            return '';
        }
    }


    public function getTotalWorkHourFormatAttribute($value){
        if(!empty($value) && $value !=Null){
            return $value;
        }else{
            return Null;
        }
    }


    public function get_attendance_timesheet($from_date, $to_date, $department_id=0, $timesheet_observation, $employee_no=0){

    	if($timesheet_observation == 'present'){
	    	return $this->get_attendance_timesheet_present($from_date, $to_date, $department_id, $employee_no);
    	}

    	if($timesheet_observation == 'archive'){
	    	return $this->get_attendance_timesheet_archive($from_date, $to_date, $department_id, $employee_no);
    	}

        if($timesheet_observation == 'both'){
            return $this->get_attendance_timesheet_both($from_date, $to_date, $department_id, $employee_no);
        }
    }


    public function get_attendance_timesheet_present($from_date, $to_date, $department_id, $employee_no){
    	$attendance = User::select('users.*')->with(['attendanceTimesheet'=>function($q)use($from_date,$to_date){
	    				$q->whereBetween('date',[$from_date,$to_date]);
	    			}])->where('users.status','1');

        if($employee_no !='0'){
            $attendance->where('users.employee_no',$employee_no);
        }

		if($department_id !=0){
			$attendance->join('designations','designations.id','=','users.designation_id')
				->join('departments','departments.id','=','designations.department_id')
				->where('departments.id',$department_id);
		}

    	return $attendance->get();
    }


    public function get_attendance_timesheet_archive($from_date, $to_date, $department_id, $employee_no){
    	$attendance = User::select('users.*')->with(['attendanceTimesheetArchive'=>function($q)use($from_date,$to_date){
	    				$q->whereBetween('date',[$from_date,$to_date]);
	    			}])->where('users.status','1');

        if($employee_no !='0'){
            $attendance->where('users.employee_no',$employee_no);
        }

		if($department_id !=0){
			$attendance->join('designations','designations.id','=','users.designation_id')
				->join('departments','departments.id','=','designations.department_id')
				->where('departments.id',$department_id);
		}

    	return $attendance->get();
    }


    public function get_attendance_timesheet_both($from_date, $to_date, $department_id, $employee_no){

        // $attendance = User::select('users.*')->with(['attendanceTimesheet'=>function($q)use($from_date,$to_date){
        //                 $q->select('*',\DB::raw('"present" as timesheet_observation'))->whereBetween('date',[$from_date,$to_date]);
        //             }])->with(['attendanceTimesheetArchive'=>function($q)use($from_date,$to_date){
        //                 $q->select('*',\DB::raw('"archive" as timesheet_observation'))->whereBetween('date',[$from_date,$to_date]);
        //             }])->where('users.status','1');

        $attendance = User::select('users.*')->with(['attendanceTimesheet'=>function($q)use($from_date,$to_date){
                        $q->whereBetween('date',[$from_date,$to_date]);
                    }])->with(['attendanceTimesheetArchive'=>function($q)use($from_date,$to_date){
                        $q->whereBetween('date',[$from_date,$to_date]);
                    }])->where('users.status','1');

        if($employee_no != '0'){
            $attendance->where('users.employee_no',$employee_no);
        }

        if($department_id !=0){
            $attendance->join('designations','designations.id','=','users.designation_id')
                ->join('departments','departments.id','=','designations.department_id')
                ->where('departments.id',$department_id);
        }

        return $attendance->get();
    }


}
