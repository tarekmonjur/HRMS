<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';
    protected $fillable = ['holiday_name', 'holiday_from', 'holiday_to', 'holiday_details', 'holiday_status'];
}
