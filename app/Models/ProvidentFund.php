<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProvidentFund extends Model
{
    protected $fillable = [
    	'user_id','pf_percent_amount','pf_effective_date','pf_interest_calculate','pf_status','approved_by','created_by','updated_by'
    ];
    

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('Y-m-d');
    }


    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value)->format('Y-m-d');
    }


    public function user(){
    	return $this->belongsTo('App\Models\User');
    }


    public function approvedBy(){
    	return $this->belongsTo('App\Models\User','approved_by','id');
    }


    public function createdBy(){
    	return $this->belongsTo('App\Models\User','created_by','id');
    }
    

    public function updatedBy(){
    	return $this->belongsTo('App\Models\User','updated_by','id');
    }


    public function details(){
        return $this->hasMany('App\Models\PfCalculation');
    }


}
