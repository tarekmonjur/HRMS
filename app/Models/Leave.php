<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'employee_leaves';


    public function leaveType(){
    	return $this->belongsTo('App\Models\LeaveType');
    }
}
