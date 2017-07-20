<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
   protected $fillable = [
   	'user_id','basic_salary','salary_in_cash','salary_month','salary_days','salary_pay_type','work_hour','overtime_hour','overtime_amount','attendance_info','allowance_info','deduction_info','total_allowance','total_deduction','perhour_salary','perday_salary','salary','gross_salary','net_salary','total_salary','remarks','created_by','updated_by'
   ];


   public function user(){
   		return $this->belongsTo('App\Models\User'); 
   }


}
