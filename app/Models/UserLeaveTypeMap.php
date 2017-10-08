<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLeaveTypeMap extends Model
{
    protected $table = "user_leave_type_maps";
    protected $fillable = [
    	'user_id', 'leave_type_id', 'number_of_days', '	active_from_year', 'active_to_year', 'status'
    ];

    public function leaveType(){
        return $this->belongsTo('App\Models\LeaveType');
    }  
}
