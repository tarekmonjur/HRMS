<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusType extends Model
{
    protected $fillable = ['bonus_type_name','bonus_type_status','bonus_type_remarks','created_by','updated_by'];


    public function createdBy(){
    	return $this->belongsTo('App\Models\User','created_by','id');
    }


    public function updatedBy(){
    	return $this->belongsTo('App\Models\User','updated_by','id');
    }


}
