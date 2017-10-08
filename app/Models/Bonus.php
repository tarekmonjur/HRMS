<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $fillable = ['user_id','bonus_type_id','bonus_amount','bonus_amount_type','bonus_type_amount','bonus_effective_date','bonus_remarks','approved_by','created_by','updated_by'];


    public function user(){
    	return $this->belongsTo('App\Models\User');
    }

    public function bonusType(){
    	return $this->belongsTo('App\Models\BonusType');
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

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d M Y');
    }

    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value)->format('d M Y');
    }
    
}
