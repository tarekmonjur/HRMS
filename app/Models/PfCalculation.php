<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PfCalculation extends Model
{
    protected $fillable = [
    	'provident_fund_id','pf_percent','pf_amount','pf_interest_percent','pf_interest_amount','pf_debit','pf_credit','pf_date','pf_observation','pf_remarks','created_by','updated_by'
    ];
}
