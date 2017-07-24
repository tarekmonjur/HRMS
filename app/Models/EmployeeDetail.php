<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    protected $fillable = ['user_id','blood_group_id','national_id','passport_no','tin_no',
        'father_name','mother_name','spouse_name','personal_email','official_email','birth_date','joining_date','resign_date',
        'phone_number','gender','marital_status','religion_id','nationality',
        'emergency_contact_person','emergency_contact_number','emergency_contact_address','created_by','updated_by'];


    public static function findUser($user_id){
         return static::where('user_id', $user_id)->first();
    }


    public function getFatherNameAttribute($value){
        return ucfirst($value);
    }


    public function getMotherNameAttribute($value){
        return ucfirst($value);
    }


    public function getJoiningDateFormatAttribute(){
    	return \Carbon\Carbon::parse($this->joining_date)->format('d M Y');
    }


    public function getBirthDateFormatAttribute(){
    	return \Carbon\Carbon::parse($this->birth_date)->format('d M Y');
    }
    

    public function bloodGroup(){
        return $this->belongsTo('App\Models\BloodGroup','blood_group_id','id');	
    }


    public function religion(){
        return $this->belongsTo('App\Models\Religion','religion_id','id'); 
    }

}
