<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeStatus extends Model
{
    protected $table = 'employee_status';

    protected $fillable = ['status_name','status','created_at','updated_at'];
}
