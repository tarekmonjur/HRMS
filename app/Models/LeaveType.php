<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_types';

    protected $fillable = ['leave_type_name', '	leave_type_number_of_days', 'leave_type_effective_for', 'level_type_details', 'leave_type_with_out_pay', 'level_type_is_remain', 'level_type_include_holiday', 'leave_type_active_from_year', 'leave_type_active_to_year', 'leave_type_created_by', 'leave_type_updated_by', 'leave_type_status'
    ];

    public function emp_types(){
		return $this->hasMany('App\Models\EmployeeType', 'id', 'leave_type_effective_for');
	}
}
