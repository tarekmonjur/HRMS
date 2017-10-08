<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmployeeTypeMap extends Model
{
    protected $fillable = ['user_id','employee_type_id','from_date','to_date','remarks','created_by','updated_by'];
}
