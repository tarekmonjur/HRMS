<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmployeeStatusMap extends Model
{
	protected $table = 'user_emp_status_map';

    protected $fillable = ['user_id','employee_status_id','from_date','remarks','document_files','created_by','updated_by','to_date','created_at','updated_at'];
}
