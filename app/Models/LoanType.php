<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $fillable = ['loan_type_name','loan_type_status','loan_type_remarks','created_by','updated_by'];


    public function createdBy(){
    	return $this->belongsTo('App\Models\User','created_by','id');
    }


    public function updatedBy(){
    	return $this->belongsTo('App\Models\User','updated_by','id');
    }
}
