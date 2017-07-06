<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AttendanceTimesheetArchive extends Model
{
    protected $fillable = ['user_id','date','observation','in_time','out_time','total_work_hour','late_count_time','late_hour','leave_type','created_at'];


    public function setInTimeAttribute($value){
        return $this->attributes['in_time'] = date('H:i',strtotime($value));
    }


    public function setOutTimeAttribute($value){
        return $this->attributes['out_time'] = date('H:i',strtotime($value));
    }
    

    public function getInTimeAttribute($value){
    	return ($value)?date('h:i A',strtotime($value)):'';
    }


    public function getOutTimeAttribute($value){
    	return ($value)?date('h:i A',strtotime($value)):'';
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
    
}
