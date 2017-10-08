<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    protected $fillable = ['shift_name','shift_start_time','shift_end_time','late_count_time','work_shift_status','created_by','updated_by'];


    public function getShiftStartTimeAttribute($value){
    	return date('h:i A',strtotime($value));
    }


    public function getShiftEndTimeAttribute($value){
    	return date('h:i A',strtotime($value));
    }


    public function getLateCountTimeAttribute($value){
    	return date('h:i A',strtotime($value));
    }


    public function getCreatedAtAttribute($value){
    	return Carbon::parse($value)->format('d M Y h:i:s A');
    }


    public function getUpdatedAtAttribute($value){
    	return Carbon::parse($value)->format('d M Y h:i:s A');
    }


    public function setShiftStartTimeAttribute($value){
    	$this->attributes['shift_start_time'] = date('H:i',strtotime($value));
    }


    public function setShiftEndTimeAttribute($value){
    	$this->attributes['shift_end_time'] = date('H:i',strtotime($value));
    }


    public function setLateCountTimeAttribute($value){
    	$this->attributes['late_count_time'] = date('H:i',strtotime($value));
    }



}
