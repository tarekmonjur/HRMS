<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{

//    protected $fillable = [];


    public function getSalaryEffectiveFormatDateAttribute($value){
        return Carbon::parse($value)->format('M d Y');
    }

    public function basicSalaryInfo(){
        return $this->belongsTo('App\Models\BasicSalaryInfo');
    }


    public function user(){
    	return $this->belongsTo('App\Models\User');
    }


}
