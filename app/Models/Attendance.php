<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

	protected $fillable = ['user_id','date','in_time','out_time','total_work_hour','late_count_time','late_hour','created_at'];
    

    public function setInTimeAttribute($value){
        return $this->attributes['in_time'] = date('H:i',strtotime($value));
    }


    public function setOutTimeAttribute($value){
        return $this->attributes['out_time'] = date('H:i',strtotime($value));
    }

    
    public function getInTimeAttribute($value){
    	return ($value != "00:00:00")?date('h:i A',strtotime($value)):"00:00:00";
    }
    

    public function getOutTimeAttribute($value){
    	return ($value != "00:00:00")?date('h:i A',strtotime($value)):"00:00:00";
    }
}
