<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceTimesheet;
use App\Models\AttendanceTimesheetArchive;
use Illuminate\Support\Facades\DB;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ArchiveAttendanceTimesheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $timeout = 120;

    protected $archiveMonth;

    protected $clearMonth;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->archiveMonth = \Config::get('hrms.attendance_archive_month');
       $this->clearMonth = \Config::get('hrms.attendance_clear_month');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        DB::transaction(function(){
            $end_date = Carbon::now()->format('Y-m-d');
            $start_date = Carbon::now()->subMonths($this->archiveMonth)->format('Y-m-d');
            $attendance_timesheet = AttendanceTimesheet::whereNotBetween('date',[$start_date, $end_date])->get();

            $all_id = [];
            $attendance = [];
            // dd($attendance_timesheet);
            foreach($attendance_timesheet as $info){
                $all_id[] = [$info->id];
                $attendance[] = [
                    "user_id"   => $info->user_id,
                    "date"      => $info->date,
                    "observation" => $info->observation,
                    "in_time"   => date('H:i',strtotime($info->in_time)),
                    "out_time"  => date('H:i',strtotime($info->out_time)),
                    "total_work_hour" => $info->total_work_hour_format,
                    'late_count_time' => $info->late_count_time,
                    'late_hour'  => $info->late_hour,
                    "leave_type" => $info->leave_type,
                    "created_at" => $info->created_at,
                    "updated_at" => $info->updated_at,
                ];
            }   

            if(count($attendance) > 0){
                AttendanceTimesheetArchive::insert($attendance);
                AttendanceTimesheet::whereIn('id',$all_id)->delete();
            }

            $end_date = Carbon::now()->format('Y-m-d');
            $start_date = Carbon::now()->subMonths($this->clearMonth)->format('Y-m-d');
            Attendance::whereNotBetween('date',[$start_date, $end_date])->delete();
        });

    }



}
