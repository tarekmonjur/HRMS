<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncrementType extends Model
{
    protected $fillable = ['increment_type_name','increment_type_status','increment_type_remarks','created_by','updated_by'];


    public function createdBy(){
    	return $this->belongsTo('App\Models\User','created_by','id');
    }


    public function updatedBy(){
    	return $this->belongsTo('App\Models\User','updated_by','id');
    }
}
