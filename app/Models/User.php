<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //employee_id will be employee_no
    protected $fillable = [
        'employee_no','designation_id','first_name','last_name', 'email', 'password','mobile_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFirstNameAttribute($value){
        return ucfirst($value);
    }

    public function getLastNameAttribute($value){
        return ucfirst($value);
    }

    public function getFullNameAttribute(){
        return ucfirst($this->first_name). ' ' .ucfirst($this->last_name);
    }

    public function getNickNameAttribute($value){
        return ucfirst($value);
    }

    public function getUserImageAttribute(){

    }
}
