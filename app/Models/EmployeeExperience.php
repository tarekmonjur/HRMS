<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{

    protected $fillable = ['user_id','company_name','position_held','job_start_date','job_end_date','job_duration','job_responsibility','job_location','created_by'];


    public function getJobDurationAttribute($value){
    	if(stristr($value,'.')){
    		$duration = explode('.', $value);
    		$month = end($duration);

    		if($month >= 10){
    			$month = str_replace('0','',$month);
    		}
    		return $duration[0].' years, '.$month.' months.';
    	}else{
    		return $value.' years, 0 months.';
    	}    	
    }


    public function setJobDurationAttribute($value){
    	if(stristr($value,'years') || stristr($value,'months')){
	    	$duration = preg_replace('/[monthsyear. ]/', '', $value);
	       	$value =  str_replace(',', '.',$duration);
       	}
    	$this->attributes['job_duration'] = $value;
    }




}
