<?php

namespace App\Http\Controllers\Attendance;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceTimesheet;
use App\Models\AttendanceTimesheetArchive;
use App\Models\WorkShiftEmployeeMap;
use App\Models\CommonWorkShift;

use App\Jobs\AttendanceTimesheetJob;
use App\Jobs\ArchiveAttendanceTimesheetJob;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    protected $auth;

    protected $user;

    protected $attendanceTimesheet;

    protected $WorkShiftEmployeeMap;

    /**
     * AttendanceController constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth, User $user, AttendanceTimesheet $attendanceTimesheet, WorkShiftEmployeeMap $WorkShiftEmployeeMap)
    {
        $this->middleware('auth:hrms');

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });

        $this->user = $user;
        $this->attendanceTimesheet = $attendanceTimesheet;
        $this->WorkShiftEmployeeMap = $WorkShiftEmployeeMap;
    }


    public function index(){
        dispatch(new AttendanceTimesheetJob());

    	$data['sidebar_hide'] = true;
    	return view('attendance.attendance')->with($data);
    }


    public function viewAttendance(Request $request){

        if($request->employee_no){
            $data['employee_no'] = $request->employee_no;
        }else{
            $data['employee_no'] = $this->auth->employee_no;
        }

        if($request->has('from_date')){
            $data['from_date'] = $request->from_date;
        }else{
            $data['from_date'] = Carbon::now()->subMonth(1)->format('Y-m-d');
        }

        if($request->has('to_date')){
            $data['to_date'] = $request->to_date;
        }else{
            $data['to_date'] = Carbon::now()->format('Y-m-d');
        }

        $data['user'] = $this->user->get_profile_info($data['employee_no']);
        return view('attendance.my_attendance')->with($data);
    }

    public function addAttendance(Request $request){

    	$this->validate($request,[
            'date' => 'required',
            'in_time' => 'required',
            'out_time' => 'required',
        ]);

        // if($request->in_time == "00:00:00" && $request->out_time == "00:00:00"){

        //     $request->offsetSet('late_count_time', "00:00:00");
        //     $request->offsetSet('late_hour', "00:00:00");
        //     $request->offsetSet('total_work_hour', "00:00:00");
        // }
        // else{

            $workShiftMap = new WorkShiftEmployeeMap;
            $emp_work_shift = $workShiftMap->get_work_shift_by_user_id_and_date($request->user_id,$request->date);

            if($emp_work_shift){
                $late_count_time =  $emp_work_shift->late_count_time;
                
                if(strtotime($request->in_time) > strtotime($late_count_time)){
                    //late
                    $late_hour = date('H.i',strtotime($request->in_time) - strtotime($emp_work_shift->shift_start_time));
                }
                else{
                    //not late
                    $late_hour = null;
                }
            }else{
                
                $common_val = CommonWorkShift::orderBy('id', 'DESC')->first();
                $late_count_time =  $common_val->common_late_count_time;

                if(strtotime($request->in_time) > strtotime($late_count_time)){
                    //late
                    $late_hour = date('H.i',strtotime($request->in_time) - strtotime($common_val->common_shift_start_time));
                }
                else{
                    //not late
                    $late_hour = null;
                }
            }

            $request->offsetSet('late_count_time',$late_count_time);
            $request->offsetSet('late_hour',$late_hour);
            $total_work_hour = date('H.i',strtotime($request->out_time) - strtotime($request->in_time));
            $request->offsetSet('total_work_hour',$total_work_hour);
        // }

        try{
            $attendance = $this->saveAttendance($request);

            if($request->ajax()){
                $data['data'] = $attendance;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Attendance Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('danger','Attendance Added!');

        }catch(\Exception $e){

            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Attendance Not Added.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Attendance Not Added!');
        }

        return redirect()->back()->withInput();
    }


    protected function saveAttendance($request){

        $observation = $this->attendanceObservation($request->date);

        if($result = Attendance::where('user_id', $request->user_id)->where('date', $request->date)->first()){
            $result->update($request->all());
        }else{
            Attendance::create($request->all());
        }

        if($request->observation == 4){
            $request->offsetSet('observation',6);
        }elseif($request->observation == 3){
            $request->offsetSet('observation',5);
        }else{
            $request->offsetSet('observation',1);
        }

        //make employee absent
        if($request->in_time == "00:00:00" && $request->out_time == "00:00:00"){
            $request->offsetSet('observation',0);
        }

        if($observation == 'present'){
            if($attend = AttendanceTimesheet::find($request->time_sheet_id)){
                $attend->update($request->all());
                $attendance = AttendanceTimesheet::find($attend->id);    
            }else{
                $attendance = AttendanceTimesheet::create($request->all());
            }
        }

        if($observation == 'archive'){
            if($attend = AttendanceTimesheetArchive::find($request->time_sheet_id)){
                $attend->update($request->all());
                $attendance = AttendanceTimesheetArchive::find($attend->id);
            }else{
                $attendance = AttendanceTimesheetArchive::create($request->all());
            }
        }

        return $attendance;
    }


    protected function attendanceObservation($date){

        //$last_row = $this->attendanceTimesheet->orderBy('date','asc')->first();
        //$last_date = date('Y-m-d',strtotime($last_row->date));
        $last_date = Carbon::now()->subMonths(\Config::get('hrms.attendance_archive_month'))->format('Y-m-d');

        if($date >= $last_date){
            return 'present';
        }elseif($date < $last_date){
            return 'archive';
        }
    }


    public function attendanceTimesheet(Request $request){

    	$this->validate($request,[
			'from_date' => 'required',
			'to_date' => 'required',
		]);

    	$from_date = $request->from_date;
    	$to_date = $request->to_date;
    	$department_id = $request->department_id;

        $timesheet_observation = $this->timesheetObservation($from_date, $to_date);

        if($request->has('employee_no')){
    		$attendance = $this->attendanceTimesheet->get_attendance_timesheet($from_date, $to_date, $department_id, $timesheet_observation, $request->employee_no);
        }else{
            $attendance = $this->attendanceTimesheet->get_attendance_timesheet($from_date, $to_date, $department_id, $timesheet_observation);
        }


		$dateData = $this->generateDays($from_date, $to_date);
    	$attendances = ['days' => $dateData['dates'], 'dayList' => $dateData['date_list'], 'attendance' => $attendance];
    	$attendanceTimesheet = $this->generateAttendanceTimesheet($attendances, $timesheet_observation);

        if($request->has('employee_no')){
            if(isset($attendanceTimesheet['attendance'][0]->attendanceTimesheets)){
                $attendanceTimesheet['attendance'] = $attendanceTimesheet['attendance'][0]->attendanceTimesheets;
                $attendanceTimesheet['report'] = $this->attendanceReport($attendanceTimesheet['attendance']);
            }else{
                $attendanceTimesheet['report'] = [];
                $attendanceTimesheet['attendance'] = [];
            }
        }

    	return $attendanceTimesheet;
    }

    protected function timesheetObservation($from_date, $to_date){

        $last_row = $this->attendanceTimesheet->orderBy('date','asc')->first();
        if(count($last_row) <= 0){
            return 'present';
        }

        $last_date = date('Y-m-d',strtotime($last_row->date));

        if($from_date >= $last_date){
            return 'present';
        }elseif(($from_date < $last_date) && ($to_date < $last_date)){
            return 'archive';
        }else{
            return 'both';
        }
    }

    protected function generateDays($from_date, $to_date){

    	$toDate = Carbon::parse($to_date);
    	$day =  $toDate->diffInDays(Carbon::parse($from_date));
        $weekend = DB::table('weekends')->where('status',1)->first();
        $weekends = [];
        if($weekend){
            $weekends = explode(',', str_replace(' ','',$weekend->weekend));
        }

    	$dates = [];
    	$dateList = [];
    	for($i=0; $i<=$day; $i++){
            $day_name = Carbon::parse($from_date)->format('l');
            if(in_array($day_name, $weekends)){
                // $dates[] = '<span style="color:red">'.Carbon::parse($from_date)->format('M d Y')."</span>";
                $dates[] = Carbon::parse($from_date)->format('M d Y (D)');
            }else{
        		$dates[] = Carbon::parse($from_date)->format('M d Y (D)');
            }
    		$dateList[] = Carbon::parse($from_date)->format('Y-m-d');
    		$from_date = Carbon::parse($from_date)->addDay(1);
    	}

    	$dateData = ['dates' => $dates, 'date_list' => $dateList];
    	return $dateData;
    }


    protected function generateAttendanceTimesheet($attendances, $timesheet_observation){
      // dd($attendances['attendance']);
    	foreach($attendances['attendance'] as $attendanceUser){

    		$timeSheet = [];

            if($timesheet_observation == 'present'){
                $attendanceTimesheet = $attendanceUser->attendanceTimesheet->toArray();
            }elseif($timesheet_observation == 'archive'){
                $attendanceTimesheet = $attendanceUser->attendanceTimesheetArchive->toArray();
            }else{
                $attendanceTimesheet = array_merge($attendanceUser->attendanceTimesheet->toArray(),$attendanceUser->attendanceTimesheetArchive->toArray());
            }
    		
    		foreach($attendances['dayList'] as $date){
    			$ck = 0;

    			if(count($attendanceTimesheet)>0){

	    			foreach($attendanceTimesheet as $key => $attendance){
		    			if($attendance['date'] == $date){
		    				$ck = 1;
                            $attendance['format_date'] = Carbon::parse($attendance['date'])->format('d M Y'); 
		    				$timeSheet[] = $attendance;
		    				unset($attendanceTimesheet[$key]);
		    			}
		    		}
		    	}

		    	if($ck==0){
		    		// $attend = new \stdClass;
    				// $attend->id = 0;
    				// $attend->user_id = $attendanceUser->id;
    				// $attend->date = $date;
    				// $attend->observation = 0;
    				// $attend->in_time = null;
    				// $attend->out_time = null;
    				// $attend->leave_type = null;
    				// $attend->created_at = $date;
                    $attend = [];
                    $attend['id'] = 0;
                    $attend['user_id'] = $attendanceUser->id;
                    $attend['date'] = $date;
                    $attend['format_date'] = Carbon::parse($date)->format('d M Y'); 
                    $attend['observation'] = 0;
                    $attend['in_time'] = '';
                    $attend['out_time'] = '';
                    $attend['total_work_hour'] = '';
                    $attend['late_count_time'] = '';
                    $attend['late_hour'] = '';
                    $attend['leave_type'] = '';
                    $attend['created_at'] = $date;
    				$timeSheet[] = $attend;
		    	}
    		}
    		
            $attendanceUser->attendanceTimesheets = $timeSheet;
    		unset($attendanceUser['attendanceTimesheet']);
    	}

    	return $attendances;
    }


    public function manualAttendance(Request $request){

        $validation = \Validator::make($request->all(),['csv_file' => 'required']);

        if($validation->fails()){
            $request->session()->flash('danger','file format is not valid!');
        }else{

            $employeeNos = User::all()->pluck('id','employee_no')->toArray();

            $csvContent = [];
            $userIds = [];
            $dates = [];

            $csv = $request->csv_file;
            $file = fopen($csv->path(), "r");

            while(!feof($file))
            {
                $content = fgetcsv($file);

                $employee_no = $content[0];
                $employee_no1 = strtolower($content[0]);
                $employee_no2 = strtolower($content[0]);

                if(isset($employeeNos[$employee_no])){
                    $user_id = $employeeNos[$employee_no];
                    
                }elseif(isset($employeeNos[$employee_no1])){
                    $user_id = $employeeNos[$employee_no1];
                    
                }elseif(isset($employeeNos[$employee_no2])){
                    $user_id = $employeeNos[$employee_no2];
                    
                }else{
                    $user_id = null;
                }

                if(!empty($user_id)){
                    $date = date('Y-m-d',strtotime($content[1]));
                    $dates[] = $date;
                    $userIds[] = $user_id;

                    $emp_work_shift = $this->WorkShiftEmployeeMap->get_work_shift_by_user_id_and_date($user_id,$date);

                    if($emp_work_shift){
                        $late_count_time =  $emp_work_shift->late_count_time;
                        
                        if(strtotime($content[2]) > strtotime($late_count_time)){
                            //late
                            $late_hour = date('H.i',strtotime($content[2]) - strtotime($emp_work_shift->shift_start_time));
                        }
                        else{
                            //not late
                            $late_hour = null;
                        }
                    }else{
                        
                        $common_val = CommonWorkShift::orderBy('id', 'DESC')->first();
                        $late_count_time =  $common_val->common_late_count_time;

                        if(strtotime($content[2]) > strtotime($late_count_time)){
                            //late
                            $late_hour = date('H.i',strtotime($content[2]) - strtotime($common_val->common_shift_start_time));
                        }
                        else{
                            //not late
                            $late_hour = null;
                        }
                    }

                    $csvContent[] = [
                        'user_id' => $user_id,
                        'date' => $date,
                        'in_time' => date('H:i',strtotime($content[2])),
                        'out_time' => date('H:i',strtotime($content[3])),
                        'total_work_hour' => date('H.i', strtotime($content[3]) - strtotime($content[2])),
                        'late_count_time' => $late_count_time,
                        'late_hour' => $late_hour,
                        'created_at' => date('Y-m-d')
                    ];
                } 
            }

            fclose($file);
            Attendance::whereIn('user_id',$userIds)->whereIn('date',$dates)->delete();

            Attendance::insert($csvContent);
            // dispatch(new AttendanceTimesheetJob($job_call = 'csv'));
            $request->session()->flash('success','Attendance successfully uploaded!');
        }

        return redirect()->back();
    }


    protected function attendanceReport($attendance){

        $absent = 0;
        $present = 0;
        $leave = 0;
        $holiday = 0;
        $weekend = 0;
        $late = 0;

        foreach($attendance as $info){
            if($info['observation'] == '0'){
                $absent++;
            }elseif($info['observation'] == '1'){
                $present++;
            }elseif($info['observation'] == '2'){
                $leave++;
            }elseif($info['observation'] == '3'){
                $holiday++;
            }elseif($info['observation'] == '4'){
                $weekend++;
            }elseif($info['observation'] == '5'){
                $present++;
                $holiday++;
            }elseif($info['observation'] == '6'){
                $present++;
                $weekend++;
            }
            if($info['late_hour']){
                $late++;
            }
        }

        $total = $present + $absent + $leave + $holiday + $weekend;

        $report = [
            'total' => $total,
            'absent' => $absent,
            'present' => $present,
            'leave' => $leave,
            'holiday' => $holiday,
            'weekend' => $weekend,
            'late' => $late,
        ];

        return $report;
    }


    public function downloadDemo(){
        $pathToFile = public_path('attendance_format_demo.csv');
        return response()->download($pathToFile, 'attendance_format.csv');
    }







}
