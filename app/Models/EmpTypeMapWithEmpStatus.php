<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpTypeMapWithEmpStatus extends Model
{
	protected $table = 'emp_type_map_with_emp_status';

    protected $fillable = ['user_emp_type_map_id','employee_status_id','from_date','remarks','document_files', 'pending_status','created_by','updated_by','to_date','created_at','updated_at'];
}
