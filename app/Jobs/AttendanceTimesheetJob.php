<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceTimesheet;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AttendanceTimesheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $timeout = 120;

    protected $calculateMonth;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->calculateMonth = \Config::get('hrms.attendance_calculate_month');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today_date = Carbon::now()->format('Y-m-d');
        $end_date = $today_date;
        $start_date = Carbon::now()->subMonths($this->calculateMonth)->format('Y-m-d');

        $attendances  = $this->attendance($start_date, $end_date);

        $today_attendance = [];
        $prev_leaves = [];
        $cancel_leaves = [];
        $prev_attendance = [];
        $userIds = [];

        $AttendanceTimesheet = AttendanceTimesheet::whereBetween('date',[$start_date, $end_date])->get();

        foreach($attendances as $attendance){
            if($attendance['date'] == $today_date){
                $today_attendance[] = $attendance;
                $userIds[] = $attendance['user_id'];
            }else{
                if($attendance['observation'] == 10){
                    $cancel_leaves[] = $attendance;
                }else{
                    $timesheet_attendance = $AttendanceTimesheet->where('user_id', $attendance['user_id'])->where('date', $attendance['date'])->first();
                    if(count($timesheet_attendance) > 0){
                        if($timesheet_attendance->observation != $attendance['observation']){
                            AttendanceTimesheet::where('user_id',$attendance['user_id'])->where('date',$attendance['date'])->update($attendance);
                        }
                    }else{
                        AttendanceTimesheet::insert($attendance);
                    }
                }
            }
        }
        
       
        if(count($userIds) > 0){
            AttendanceTimesheet::where('date',$today_date)->whereIn('user_id',$userIds)->delete();
        }

        if(count($today_attendance) > 0){
            AttendanceTimesheet::insert($today_attendance);
        }

        // update cancel leaves data
        foreach ($cancel_leaves as $info) {
            $info['observation'] = 0;
            AttendanceTimesheet::where('user_id',$info['user_id'])->where('date',$info['date'])->update($info);
        }

    }


    protected function attendance($start_date, $end_date)
    {
        
        $days = $this->generateDays($start_date, $end_date);
        $company_weekend_days = $this->getCompanyWeekend();

        $users = User::with([
            'attendance' => function($q)use($start_date, $end_date){ $q->whereBetween('date',[$start_date, $end_date]);},
            'leaves.leaveType',
            'leaves' => function($q){$q->where('employee_leave_status',3);},
            'cancel_leaves' => function($q){$q->where('employee_leave_status',4);},
            'workShifts' => function($q)use($start_date, $end_date){
                $q->where('status',1);
            }])->get();

        $get_holidays = DB::table('holidays')->where('holiday_status',1)->get();
        $holidays = $this->generateHoliday($get_holidays);

        $attendanceResult = [];
        //----
        // $sakib = 0;
        
        //----
        foreach ($users as $user) {
            $attendance_array = $user->attendance->pluck('date')->toArray();

            //-------
            $day_inTime_outTime_ary = [];   
            $final_days = []; 
            // $sakib++;
            
            // if($sakib == 2){

                $all_atten_data = $user->attendance->toArray();
                            
                $counter = 0;

                foreach($all_atten_data as $key => $value){
                    foreach($value as $key => $val){
                        if($key == 'date'){
                            $day_inTime_outTime_ary[$counter]['date'] = $val;
                        }

                        if($key == 'in_time'){
                            $day_inTime_outTime_ary[$counter]['in_time'] = $val;
                        }

                        if($key == 'out_time'){
                            $day_inTime_outTime_ary[$counter]['out_time'] = $val;
                        }
                    }

                    $counter++;
                }

                foreach($day_inTime_outTime_ary as $info){
                    if($info['in_time'] == "00:00:00" && $info['out_time'] == "00:00:00"){
                        
                    }else{
                        $final_days[] = $info['date'];
                    }
                }

                // dd($day_inTime_outTime_ary);
                // dd($attendance_array);
            // }
            //-------

            $attendance_list = $user->attendance;
            $leaves = $this->generateLeaves($user->leaves);

            if(count($user->cancel_leaves) > 0){
                
                $leave_user_id = $user->cancel_leaves[0]->user_id;
                $cancel_leaves = $this->generateLeaves($user->cancel_leaves);
            }else{
                $cancel_leaves = [];
            }
            $weekends = $this->generateWeekend($user->workShifts, $days, $company_weekend_days);

            // dd($days);

            foreach($days as $key => $day){
                $leave_type = '';
                
                // if(!in_array($day, $attendance_array)){
                //--------------------------
                if(!in_array($day, $final_days)){
                    // echo $day."<br/>";
                //--------------------------

                    if(array_key_exists($day, $leaves)){
                        $observation = 2;
                        $leave_type = $leaves[$day];
                    }elseif(array_key_exists($day,$cancel_leaves)){
                            $observation = 10; //for cancel leaves
                    }elseif(in_array($day,$holidays)){
                        $observation = 3;
                    }elseif(in_array($day, $weekends)){
                        $observation = 4;
                    }else{
                        $observation = 0;
                    }

                    $attendanceResult[] = [
                        'user_id' => $user->id,
                        'date' => $day,
                        'observation' => $observation,
                        'in_time' => Null,
                        'out_time' => Null,
                        'total_work_hour' => Null,
                        'late_count_time' => Null,
                        'late_hour' => Null,
                        'leave_type' => $leave_type
                    ];

                }else{
                    // echo "---------".$day."<br/>";
                    if(in_array($day, $holidays)){
                        $observation = 5;
                    }elseif(in_array($day,$weekends)){
                        $observation = 6;
                    }else{
                        $observation = 1;
                    }

                    $attendance = $attendance_list->where('date',$day)->first();
                    
                    $attendanceResult[] = [
                        'user_id' => $attendance->user_id,
                        'date' => $attendance->date,
                        'observation' => $observation,
                        'in_time' => date('H:i',strtotime($attendance->in_time)),
                        'out_time' => date('H:i',strtotime($attendance->out_time)),
                        'total_work_hour' => $attendance->total_work_hour,
                        'late_count_time' => date('H:i',strtotime($attendance->late_count_time)),
                        'late_hour' => $attendance->late_hour,
                        'leave_type' => $leave_type
                    ];
                }
            }
        }

    
        
        return $attendanceResult;
    }


    protected function generateDays($from_date, $to_date){

        $toDate = Carbon::parse($to_date);
        $day =  $toDate->diffInDays(Carbon::parse($from_date));

        $dates = [];
        for($i=0; $i<=$day; $i++){
            $dates[] = Carbon::parse($from_date)->format('Y-m-d');
            $from_date = Carbon::parse($from_date)->addDay(1);
        }
        return $dates;
    }


    protected function generateLeaveDays($from_date, $to_date, $leave_type){

        $toDate = Carbon::parse($to_date);
        $day =  $toDate->diffInDays(Carbon::parse($from_date));

        $dates = [];
        for($i=0; $i<=$day; $i++){
            $date = Carbon::parse($from_date)->format('Y-m-d');
            $dates[$date] = $leave_type;
            $from_date = Carbon::parse($from_date)->addDay(1);
        }
        return $dates;
    }


    protected function generateLeaves($leaves){
        // dd($leaves);
        $days = [];
        foreach($leaves as $leave){
            $leaveType = $leave->leaveType->leave_type_name;
            $day = $this->generateLeaveDays($leave->employee_leave_from, $leave->employee_leave_to, $leaveType);
            $days = array_merge($days,$day);
        }
        // dd($days);
        return $days;
    }


    protected function generateHoliday($holidays){
        $days = [];
        foreach($holidays as $holiday){
            $day = $this->generateDays($holiday->holiday_from, $holiday->holiday_to);
            $days = array_merge($days,$day);
        }
        // dd($days);
        return $days;
    }


    protected function getCompanyWeekend()
    {
        $company_weekend = DB::table('weekends')->where('status',1)->first();
        $company_weekend_day = [];
        if($company_weekend){
            $company_weekend_day = explode(',', str_replace(' ','',$company_weekend->weekend));
        }

       // dd($company_weekend_dates);
       return $company_weekend_day;
    }


    protected function generateWeekend($workShifts, $dates, $company_weekend_days)
    {
        $weekends = [];
        $workshift_dates = [];
        $weekend_dates = [];
        $company_weekend_dates = [];

        foreach($workShifts as $workShift)
        {
            $workshift_date = $this->generateDays($workShift->start_date, $workShift->end_date);
            $workshift_dates = array_merge($workshift_dates,$workshift_date);

            $weekend_date = $this->calculateWeekend($workshift_date, $workShift->work_days);
            $weekend_dates = array_merge($weekend_dates,$weekend_date);
        }

        if(count($workshift_dates) > 0)
        {
            $regular_work_dates = collect($dates)->diff($workshift_dates);
            $company_weekend_dates = $this->calculateCompanyWeekend($company_weekend_days, $regular_work_dates);
            $weekends = array_merge($company_weekend_dates, $weekend_dates);
            // dd($dates, $workshift_dates, $regular_work_dates, $company_weekend_dates, $weekend_dates, $weekends);
        }
        else
        {
            $weekends = $this->calculateCompanyWeekend($company_weekend_days, $dates);
        }

        return $weekends;
    }


    protected function calculateCompanyWeekend($company_weekend_days, $dates)
    {
        $company_weekend_dates = [];
        foreach($dates as $date)
        {
            $day_name = Carbon::parse($date)->format('l');
            if(in_array($day_name, $company_weekend_days))
            {
                $company_weekend_dates[] = $date;
            }
       }
       // dd($company_weekend_dates);
       return $company_weekend_dates;
    }


    protected function calculateWeekend($dates, $days)
    {
        $days = explode(',', $days);
        $weekends = [];

        foreach($dates as $date){
            $day = date('D',strtotime($date));

            if($day == 'Sat'){
                $day_num = 1;
            }elseif($day == 'Sun'){
                $day_num = 2;
            }elseif($day == 'Mon'){
                $day_num = 3;
            }elseif($day == 'Tue'){
                $day_num = 4;
            }elseif($day == 'Wed'){
                $day_num = 5;
            }elseif($day == 'Thu'){
                $day_num = 6;
            }elseif($day == 'Fri'){
                $day_num = 7;
            }

            if(!in_array($day_num,$days)){
                $weekends[] = $date;
            }
        }

        return $weekends;
    }



}
