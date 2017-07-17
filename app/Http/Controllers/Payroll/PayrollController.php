<?php

namespace App\Http\Controllers\Payroll;

use App\Models\User;
use App\Models\Salary;
use App\Models\EmployeeSalary;
use App\Models\AttendanceTimesheet;

use App\Services\CommonService;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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


    public function addSalary(Request $request)
    {
    	try{

	    	$salaries = [
		    	'user_id' => $request->user_id,
		    	'basic_salary' => floatval(str_replace(',','',$request->basic_salary)),
		    	'salary_in_cash' => floatval(str_replace(',','',$request->salary_in_cash)),
		    	'salary_month' => $request->salary_month,
		    	'salary_days' => $request->payment_days,
		    	'salary_pay_type' => $request->salary_pay_type,
		    	'overtime_hour' => 0,
		    	'overtime_amount' => 0.00,
		    	'attendance_info' => serialize($request->attendances),
		    	'total_allowance' => floatval(str_replace(',','',$request->total_allowance)),
		    	'total_deduction' => floatval(str_replace(',','',$request->total_deduction)),
		    	'total_salary' => floatval(str_replace(',','',$request->total_salary)),
		    	'gross_salary' => floatval(str_replace(',','',$request->gross_salary)),
	    	];

    		// dd($salaries);
	    	// dd($request->all());

	    	$salarys = Salary::where('user_id', $request->user_id)
		    		->where('salary_month', $request->salary_month)
		    		->get();

			DB::beginTransaction();

	    	if($request->salary_pay_type == 'full')
	    	{
	    		$salary_partial = $salarys->where('salary_pay_type', 'partial')->count();
	    		if($salary_partial > 0){
	    			$data['status'] = 'warning';
		            $data['statusType'] = 'OK';
		            $data['code'] = 200;
		            $data['title'] = 'Warning!';
		            $data['message'] = "You can't provide full month salary for this employee. Cause this month he/she already taken partial salary.";
		            return response()->json($data,200);
	    		}

	    		$salary_full = $salarys->where('salary_pay_type', 'full')->count();
	    		// return $salary;
	    		if($salary_full <= 0){
	    			$salaries['created_by'] = $this->auth->id;
	    			$salaries['created_at'] = Carbon::now()->format('Y-m-d h:i:s');
	    			Salary::insert($salaries);
	    		}
	    		else
	    		{
	    			$salaries['updated_by'] = $this->auth->id;
	    			$salaries['updated_at'] = Carbon::now()->format('Y-m-d h:i:s');

	    			Salary::where('user_id', $request->user_id)
			    		->where('salary_month', $request->salary_month)
			    		->where('salary_pay_type', $request->salary_pay_type)
			    		->update($salaries);
	    		}
	    	}
	    	elseif($request->salary_pay_type == 'partial')
	    	{
	    		$salary = $salarys->where('salary_pay_type', 'full')->count();
	    		// dd($salary);
	    		if($salary > 0){
    				$data['status'] = 'warning';
		            $data['statusType'] = 'OK';
		            $data['code'] = 200;
		            $data['title'] = 'Warning!';
		            $data['message'] = 'This employee full salary already added.';
		            return response()->json($data,200);
    			}

	    		$salary_pay_days = $request->payment_days;
	    		$already_pay_day = $salarys->where('salary_pay_type', 'partial')->sum('salary_days');
	    		$present_month_days = Carbon::parse($request->salary_month)->daysInMonth;
	    	
	    		if($present_month_days > $already_pay_day ){
	    			$salary_due_days = $present_month_days - $already_pay_day;
	    			if($salary_due_days < $salary_pay_days){
	    				$data['status'] = 'warning';
			            $data['statusType'] = 'OK';
			            $data['code'] = 200;
			            $data['title'] = 'Warning!';
			            $data['message'] = 'This employee already taken '.$already_pay_day.' days salary.';
			            return response()->json($data,200);
	    			}
	    		}else{
	    			$data['status'] = 'warning';
		            $data['statusType'] = 'OK';
		            $data['code'] = 200;
		            $data['title'] = 'Warning!';
		            $data['message'] = 'This employee full salary already added.';
		            return response()->json($data,200);
	    		}

	    		$salary = $salarys->where('salary_pay_type', 'full')->count();
    			$salaries['created_by'] = $this->auth->id;
    			$salaries['created_at'] = Carbon::now()->format('Y-m-d h:i:s');
    			Salary::insert($salaries);
	    	}

	    	DB::commit();
            $data['status'] = 'success';
            $data['statusType'] = 'OK';
            $data['code'] = 200;
            $data['title'] = 'Success!';
            $data['message'] = 'Salary Successfully Added!';
            return response()->json($data,200);

	    }catch(\Exception $e){
	    	DB::rollback();
	    	if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Salary Not Added!';
                return response()->json($data,500);
            }
	    }
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

    	$user_ids = $this->getUserIds($branch_id, $department_id, $unit_id, $user_id, $salary_month);
    	$userInfos = $this->getUserInformations($user_ids, $salary_type, $salary_month, $salary_day);

        $days = $userInfos['days'];
    	$month_days = $userInfos['month_days'];
    	$userInfo = $userInfos['userInfo'];
    	$allowance_and_deduction = $userInfos['allowance_and_deduction'];
    	// dd($userInfo);
    	// dd($allowance_and_deduction);
    	if(Carbon::parse($salary_month)->format('m') == Carbon::now()->format('m'))
    	{
	    	$maybe_present = Carbon::parse(Carbon::parse($salary_month)->format('Y-m-t'))->diff(Carbon::now())->days;
    	}else{
    		$maybe_present = 0;
    	}

        //dd($maybe_present);

    	$salary_reports = [];

    	foreach($userInfo as $user)
    	{
    		$salary = 0;
	    	$allowances = [];
    		$deductions = [];
    		$total_allowance = 0;
    		$total_deduction = 0;

    		$user_id = $user->id;
    		$work_hour = 8;
            $basic_salary = $user->basic_salary;
    		$salary_in_cash = $user->salary_in_cache;
            // $perday_salary = $basic_salary / $days;
    		$perday_salary = ($basic_salary + $salary_in_cash) / $month_days;
    		$per_hour_salary = $perday_salary / $work_hour;

    		$all_attendance = $user->attendanceTimesheet;

    		if($salary_type == 'month')
    		{
    			$salary_pay_type = 'full';
    			// $all_attendance = $user->attendanceTimesheet;
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

    			$allowance_deduction = $allowance_and_deduction->where('user_id',$user_id)->all();

    			foreach($allowance_deduction as $info)
    			{
    				$percent = 0;
    				$salary_amount = $info->salary_amount;

    				if($info->basicSalaryInfo->salary_info_type == 'allowance')
    				{
    					if($info->salary_amount_type == 'fixed'){
    						$total_allowance = $total_allowance + $salary_amount;
    					}elseif($info->salary_amount_type == 'percent'){
    						$percent = $salary_amount;
    						$salary_amount = (($basic_salary + $salary_in_cash) * $salary_amount)/100;
    						$total_allowance = $total_allowance + $salary_amount;
    					}

    					$allowances[] = [
    						'name' => $info->basicSalaryInfo->salary_info_name,
    						'amount_type' => $info->salary_amount_type,
    						'percent' => $percent,
    						'amount' => $salary_amount,
    						'effective_date' => $info->salary_effective_date,
    					];

    				}
    				elseif($info->basicSalaryInfo->salary_info_type == 'deduction')
    				{
    					if($info->salary_amount_type == 'fixed'){
    						$total_deduction = $total_deduction + $salary_amount;
    					}elseif($info->salary_amount_type == 'percent'){
    						$percent = $salary_amount;
    						$salary_amount = (($basic_salary + $salary_in_cash) * $salary_amount)/100;
    						$total_deduction = $total_deduction + $salary_amount;
    					}

    					$deductions[] = [
    						'name' => $info->basicSalaryInfo->salary_info_name,
    						'percent' => $percent,
    						'amount' => $salary_amount,
    						'amount_type' => $info->salary_amount_type,
    						'effective_date' => $info->salary_effective_date,
    					];
    				}
    			}
    		}
    		elseif($salary_type == 'day')
    		{
	    		$salary_pay_type = 'partial';
	    		$attendances = [];
    			$payment_days = $days;
    		}

			$total_work_hour = $all_attendance->whereIn('observation',[1,5,6])->sum('total_work_hour');
   //  		if($user->employee_type_id == 3){
   //  			$salary = $per_hour_salary * $total_work_hour;
   //  		}else{
			// 	$salary = $perday_salary * $payment_days;
			// }

			$salary = $perday_salary * $payment_days;

			$total_salary = ($salary+$total_allowance) - $total_deduction;
			$gross_salary = $total_salary;
    		
    		$salary_reports[] = (object)[
    			'user_id'=> $user->id,
    			'employee_no' =>  $user->employee_no,
    			'full_name' => $user->fullname,
    			'employee_type_id' => $user->employee_type_id,
    			'employee_type' => $user->employeeType->type_name,
    			'basic_salary' => number_format($basic_salary, 2),
                'salary_effective_date' => $user->effective_date,
    			'salary_in_cash' => $user->salary_in_cache,
    			'salary_month' => $salary_month,
    			'days' => $days,
    			'salary_pay_type' => $salary_pay_type,
    			'payment_days' => $payment_days,
    			'attendances' => $attendances,
    			'allowances'=> $allowances,
    			'total_allowance' => $total_allowance,
    			'deductions'=> $deductions,
    			'total_deduction' => $total_deduction,
    			'work_hour' => $work_hour,
    			'total_work_hour' => $total_work_hour,
    			'perhour_salary' => number_format($per_hour_salary, 2),
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


    protected function getUserIds($branch_id, $department_id, $unit_id, $user_id, $salary_month)
    {
    	if($user_id !=0){
    		$user_ids = [$user_id];
    	}elseif($branch_id !=0 || $department_id !=0 || $unit_id !=0){
    		$users = $this->getEmployeeByDepartmentUnitBranch($branch_id, $department_id, $unit_id);
    		$user_ids = $users->where('effective_date','<=',Carbon::parse($salary_month)->format('Y-m-t'))->pluck('id');
    	}else{
    		$users = User::where('status',1)->get();
    		$user_ids = $users->where('effective_date','<=',Carbon::parse($salary_month)->format('Y-m-t'))->pluck('id');
    	}
    	return $user_ids;
    }


    protected function getUserInformations($user_ids, $salary_type, $salary_month, $salary_day)
    {
		$start_date = Carbon::parse($salary_month)->format('Y-m-d');
		$end_date = Carbon::parse($salary_month)->format('Y-m-t');
        $month_days = Carbon::parse($salary_month)->daysInMonth;

    	if($salary_type == 'month'){
    		$days = $month_days;

			$userInfo = User::with(['employeeType','attendanceTimesheet'=>function($q)use($start_date, $end_date){
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
			->whereIn('user_id', $user_ids)
			->where('salary_effective_date','<=',$end_date)
			->get();

    	return ['days' => $days, 'month_days' =>$month_days,  'userInfo' => $userInfo, 'allowance_and_deduction' => $allowance_and_deduction];
    }



}
