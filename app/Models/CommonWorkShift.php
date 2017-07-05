<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CommonWorkShift extends Model
{
    protected $fillable = ['common_shift_name','common_shift_start_time','common_shift_end_time','common_late_count_time','common_work_shift_status','created_by','updated_by'];
}
