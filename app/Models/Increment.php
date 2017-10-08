<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Increment extends Model
{
    protected $fillable = ['user_id','increment_type_id','increment_amount','increment_amount_type','increment_type_amount','increment_effective_date','increment_remarks','approved_by','created_by','updated_by'];


    public function user(){
    	return $this->belongsTo('App\Models\User');
    }

    public function incrementType(){
    	return $this->belongsTo('App\Models\IncrementType');
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
}
