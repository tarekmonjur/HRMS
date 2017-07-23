<?php

namespace App\Http\Controllers\Payroll;

use App\Models\User;
use App\Models\Salary;

use App\Services\CommonService;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{

	use CommonService;

	protected $auth;

    public function __construct(Auth $auth){
    	$this->middleware('auth:hrms');

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
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
    	return view('payroll.report')->with($data);
    }


    public function salaries(Request $request)
    {
    	if($request->ajax()){
    		$this->validate($request,[
	            'salary_month' => 'required',
	        ]);

	        $branch_id = $request->branch_id;
	    	$department_id = $request->department_id;
	    	$unit_id = $request->unit_id;
	    	$user_id = $request->user_id;
	    	$salary_month = $request->salary_month;

	        $user_ids = $this->getUserIds($branch_id, $department_id, $unit_id, $user_id, $salary_month);
	        $salaries = Salary::with('user.details','user.designation','user.unit.department')
	        			->whereIn('user_id', $user_ids)
	        			->where('salary_month', $salary_month)
	        			->get();

          	// $salaries = Salary::get();

			$salary_reports = [];
	        foreach($salaries as $salary)
	        {
				$salary_reports[] = (object)[
	    			'user_id'=> $salary->user_id,
	    			'employee_no' =>  $salary->user->employee_no,
	    			'full_name' => $salary->user->fullname,
	    			'joining_date' => $salary->user->details->joining_date,
	    			'department' => $salary->user->unit->department->department_name,
	    			'designation' => $salary->user->designation->designation_name,
	    			'basic_salary' => number_format($salary->basic_salary, 2),
	    			'salary_in_cash' => $salary->salary_in_cash,
	    			'salary_month' => $salary->salary_month,
	    			'salary_month_format' => Carbon::parse($salary->salary_month)->format('M Y'),
	    			'salary_pay_type' => $salary->salary_pay_type,
	    			'salary_days' => $salary->salary_days,
	                'overtime_hour' => $salary->overtime_hour,
	                'overtime_amount' => $salary->overtime_amount,
	    			'attendances' => unserialize($salary->attendance_info),
	    			'allowances'=> unserialize($salary->allowance_info),
	    			'total_allowance' => $salary->total_allowance,
	    			'deductions'=> unserialize($salary->deduction_info),
	    			'total_deduction' => $salary->total_deduction,
	    			'work_hour' => $salary->work_hour,
	    			'perhour_salary' => round($salary->perhour_salary),
	    			'perday_salary' => round($salary->perday_salary),
	    			'salary' => round($salary->salary),
	                'gross_salary' => round($salary->gross_salary),
	    			'net_salary' => round($salary->net_salary),
	                'total_salary' => round($salary->total_salary),
	                'remarks' => $salary->remarks
	    		];
	        }			

	        return $salary_reports;
	        // dd($salaries);
    	}
    }


    protected function getUserIds($branch_id, $department_id, $unit_id, $user_id, $salary_month)
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


}
