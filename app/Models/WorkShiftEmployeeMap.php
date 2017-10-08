<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WorkShiftEmployeeMap extends Model
{

	protected $fillable = ['user_id','work_shift_id','work_days','start_date','end_date','status','created_by','updated_by'];

	public function getStartDateFormatAttribute(){
		if(!empty($this->start_date) && $this->start_date !=null){
			return Carbon::parse($this->start_date)->format('d M Y');
		}else{
			return $this->start_date;
		}
	}
	

	public function getEndDateFormatAttribute(){
		if(!empty($this->end_date) && $this->end_date !=null){
			return Carbon::parse($this->end_date)->format('d M Y');
		}else{
			return $this->end_date;
		}
	}


    public function workShift(){
    	return $this->belongsTo('App\Models\WorkShift');
    }


    public function get_work_shift_by_user_id_and_date($user_id, $date)
    {
        return $this->where('user_id',$user_id)->where('work_shift_employee_maps.status',1)
				->where('start_date','<=',$date)->where('end_date','>=',$date)
				->join('work_shifts','work_shifts.id','=','work_shift_employee_maps.work_shift_id')
            	->first();
    }


}
