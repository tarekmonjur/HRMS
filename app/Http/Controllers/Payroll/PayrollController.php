<?php

namespace App\Http\Controllers\Payroll;

use App\Models\User;
use App\Models\EmployeeSalary;
use App\Models\AttendanceTimesheet;

use App\Services\CommonService;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PayrollController extends Controller
{
	use CommonService;

	protected $auth;

    public function __construct(Auth $auth, AttendanceTimesheet $attendanceTimesheet){
    	$this->middleware('auth:hrms');

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });

        $this->attendanceTimesheet = $attendanceTimesheet;

    }


    public function index(Request $request)
    {
    	if($request->ajax()){
    		if($request->isMethod('post')){
	    		return $this->generateSalary($request);
    		}else{
    			return $this->getEmployeeByDepartmentUnitBranch($request->segment(3), $request->segment(4), $request->segment(5));
    		}
    	}

    	$data['sidebar_hide'] = true;
    	$data['departments'] = $this->getDepartments();
    	$data['branches'] = $this->getBranches();
    	return view('payroll.payroll')->with($data);
    }


    public function generateSalary($request)
    {
    	$this->validator($request);

    	$branch_id = $request->branch_id;
    	$department_id = $request->department_id;
    	$unit_id = $request->unit_id;
    	$user_id = $request->user_id;
    	$salary_type = $request->salary_type;
    	$salary_month = $request->salary_month;
    	$salary_day = $request->salary_day;

    	$user_ids = $this->getUserIds($branch_id, $department_id, $unit_id, $user_id);
    	$userInfos = $this->getUserInformations($user_ids, $salary_type, $salary_month, $salary_day);

    	$days = $userInfos['days'];
    	$userInfo = $userInfos['userInfo'];
    	$allowance_and_deduction = $userInfos['allowance_and_deduction'];
    	// dd($userInfo);
    	// dd($allowance_and_deduction);
    	if(Carbon::parse($salary_month)->format('m') == Carbon::now()->format('m')){
	    	$maybe_present = Carbon::parse(Carbon::parse($salary_month)->format('Y-m-t'))->diff(Carbon::now())->days;
    	}else{
    		$maybe_present = 0;
    	}
    	$salary_reports = [];

    	foreach($userInfo as $user)
    	{
    		$salary = 0;
	    	$allowances = [];
    		$deductions = [];
    		$total_allowance = 0;
    		$total_deduction = 0;

    		$user_id = $user->id;
    		$basic_salary = $user->basic_salary;
    		$perday_salary = $basic_salary / $days;

    		if($salary_type == 'month')
    		{
    			$all_attendance = $user->attendanceTimesheet;
	    		$attendance_absent = $all_attendance->where('observation',0)->count();
	    		$attendance_present = $all_attendance->whereIn('observation',[1,5,6])->count();
	    		$attendance_leave = $all_attendance->where('observation',2)->count();
	    		$attendance_holiday = $all_attendance->where('observation',3)->count();
	    		$attendance_weekend = $all_attendance->where('observation',4)->count();
	    		$attendance_late = $all_attendance->where('late_hour','!=',null)->count();

	    		$attendance_present = $attendance_present + $maybe_present;

	    		$total_attendance = $attendance_absent + $attendance_present + $attendance_leave + $attendance_holiday + $attendance_weekend;
	    		$attendance_absent = $attendance_absent + ($days - $total_attendance);
	    		$payment_days = $days - $attendance_absent;

	    		$attendances = [
	    			'attendance_absent' => $attendance_absent,
	    			'attendance_present' => $attendance_present,
	    			'attendance_leave' => $attendance_leave,
	    			'attendance_holiday' => $attendance_holiday,
	    			'attendance_weekend' => $attendance_weekend,
	    			'attendance_late' => $attendance_late,
	    		];

    			$salary = $perday_salary * $payment_days;
    			$allowance_deduction = $allowance_and_deduction->where('user_id',$user_id)->all();

    			foreach($allowance_deduction as $info)
    			{
    				if($info->basicSalaryInfo->salary_info_type == 'allowance'){
    					$allowances[] = [
    						'name' => $info->basicSalaryInfo->salary_info_name,
    						'amount' => $info->salary_amount,
    						'amount_type' => $info->salary_amount_type,
    						'effective_date' => $info->salary_effective_date,
    					];

    					if($info->salary_amount_type == 'fixed'){
    						$total_allowance = $total_allowance + $info->salary_amount;
    					}elseif($info->salary_amount_type == 'percent'){
    						$total_allowance = $total_allowance + (($basic_salary * $info->salary_amount)/100);
    					}

    				}elseif($info->basicSalaryInfo->salary_info_type == 'deduction'){
    					$deductions[] = [
    						'name' => $info->basicSalaryInfo->salary_info_name,
    						'amount' => $info->salary_amount,
    						'amount_type' => $info->salary_amount_type,
    						'effective_date' => $info->salary_effective_date,
    					];

    					if($info->salary_amount_type == 'fixed'){
    						$total_deduction = $total_deduction + $info->salary_amount;
    					}elseif($info->salary_amount_type == 'percent'){
    						$total_deduction = $total_deduction + (($basic_salary * $info->salary_amount)/100);
    					}
    				}
    			}

    			$total_salary = ($salary+$total_allowance) - $total_deduction;
    			$gross_salary = $total_salary;
    		}
    		elseif($salary_type == 'day')
    		{
	    		$attendances = [];

    			$payment_days = $days;
    			$salary = $perday_salary * $days;
    			$total_salary = $salary;
    			$gross_salary = $basic_salary;
    		}
    		
    		$salary_reports[] = (object)[
    			'user_id'=> $user->id,
    			'employee_no' =>  $user->employee_no,
    			'full_name' => $user->fullname,
    			'basic_salary' => number_format($basic_salary, 2),
    			'salary_in_cash' => $user->salary_in_cache,
    			'salary_month' => $salary_month,
    			'days' => $days,
    			'payment_days' => $payment_days,
    			'attendances' => $attendances,
    			'allowances'=> $allowances,
    			'total_allowance' => $total_allowance,
    			'deductions'=> $deductions,
    			'total_deduction' => $total_deduction,
    			'perday_salary' => number_format($perday_salary, 2),
    			'salary' => number_format($salary, 2),
    			'total_salary' => number_format($total_salary, 2),
    			'gross_salary' => number_format($gross_salary, 2)
    		];
    	}
    	// dd($salary_reports);
    	return $salary_reports;
    }


    protected function validator($request){
    	$this->validate($request,[
            'salary_month' => 'required',
            'salary_type' => 'required',
            'salary_day' => 'required_if:salary_type,day|numeric|max:356',
        ],[],[
        	'salary_type' => 'type',
        	'salary_month' => 'month',
        ]);
    }


    protected function getUserIds($branch_id, $department_id, $unit_id, $user_id)
    {
    	if($user_id !=0){
    		$user_ids = [$user_id];
    	}elseif($branch_id !=0 || $department_id !=0 || $unit_id !=0){
    		$users = $this->getEmployeeByDepartmentUnitBranch($branch_id, $department_id, $unit_id);
    		$user_ids = $users->pluck('id');
    	}else{
    		$users = User::where('status',1)->get();
    		$user_ids = $users->pluck('id');
    	}
    	return $user_ids;
    }


    protected function getUserInformations($user_ids, $salary_type, $salary_month, $salary_day)
    {
		$start_date = Carbon::parse($salary_month)->format('Y-m-d');
		$end_date = Carbon::parse($salary_month)->format('Y-m-t');

    	if($salary_type == 'month'){
    		$days = Carbon::parse($salary_month)->daysInMonth;

			$userInfo = User::with(['attendanceTimesheet'=>function($q)use($start_date, $end_date){
					$q->whereBetween('date', [$start_date, $end_date]);
				},'providentFund'=>function($q)use($start_date, $end_date){
					$q->where('pf_status',1)->where('approved_by','!=',0)
						->whereBetween('pf_effective_date',[$start_date,$end_date]);
				},'loan'=>function($q)use($start_date, $end_date){
					$q->where('loan_status',1)
						->where('approved_by','!=',0)
						->where('loan_start_date','<=',$start_date)
						->where('loan_end_date','=>',$end_date);
				}])
				->whereIn('id', $user_ids)->get();
    	}
    	elseif($salary_type == 'day')
    	{
    		$days = $salary_day;

			$userInfo = User::with(['providentFund'=>function($q)use($start_date, $end_date){
					$q->where('pf_status',1)->where('approved_by','!=',0)
						->whereBetween('pf_effective_date',[$start_date,$end_date]);
				},'loan'=>function($q)use($start_date, $end_date){
					$q->where('loan_status',1)
						->where('approved_by','!=',0)
						->where('loan_start_date','<=',$start_date)
						->where('loan_end_date','=>',$end_date);
				}])
				->whereIn('id', $user_ids)->get();
    	}

    	$allowance_and_deduction =  EmployeeSalary::with('basicSalaryInfo')
			->whereBetween('salary_effective_date',[$start_date,$end_date])
			->whereIn('user_id', $user_ids)
			->get();

    	return ['days' => $days, 'userInfo' => $userInfo, 'allowance_and_deduction' => $allowance_and_deduction];
    }



}
