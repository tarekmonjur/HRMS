<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

	protected $fillable = ['user_id','date','in_time','out_time','total_work_hour','created_at'];
    

    public function setInTimeAttribute($value){
        return $this->attributes['in_time'] = date('h:i',strtotime($value));
    }


    public function setOutTimeAttribute($value){
        return $this->attributes['out_time'] = date('h:i',strtotime($value));
    }

    
    public function getInTimeAttribute($value){
    	return date('h:i A',strtotime($value));
    }
    

    public function getOutTimeAttribute($value){
    	return date('h:i A',strtotime($value));
    }
}
