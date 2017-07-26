<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\User;
use App\Models\Level;
use App\Models\Units;
use App\Models\Bank;
use App\Models\BasicSalaryInfo;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeType;
use App\Models\Division;
use App\Models\District;
use App\Models\PoliceStation;
use App\Models\BloodGroup;
use App\Models\EducationLevel;
use App\Models\Institute;
use App\Models\Degree;
use App\Models\Language;
use App\Models\Religion;
use App\Models\UserLeaveTypeMap;
use App\Models\EmployeeLeave;

use App\Models\WorkShift;
use App\Models\BonusType;
use App\Models\IncrementType;
use App\Models\LoanType;

use App\Models\Setting;
use App\Models\Setup\Config;

use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

trait CommonService
{
    
    public function settings(){
        $settings = Setting::all();
        foreach ($settings as $setting) {
            Session([$setting->field_name => $setting->field_value]);
        }
    }

    public function getEmployeeType(){
        return EmployeeType::where('status',1)->get();
    }

    public function getEmployee(){
        return User::with('designation')->where('status',1)->get();
    }

    public function getBranches(){
        return Branch::where('branch_status',1)->get();
    }


    public function getDepartments(){
        return Department::where('status',1)->get();
    }


    public function getLevels(){
        return Level::where('status',1)->get();
    }


	public function getDesignations(){
		return Designation::with('department','level')->where('status',1)->orderBy('id','desc')->get();
	}


    public function getUnits(){
        return Units::where('unit_status',1)->get();
    }


    public function getUnitByDepartmentId($id){
        return Units::where('unit_departments_id',$id)->get();
    }


    public function getUnitByDesignationId($id){
        $units =  Designation::with('department.units')->orderBy('id','desc')->find($id);
        return $units->department->units;
    }


    public function levelRecursive($parentRecursive,$data){
        if($parentRecursive){
            array_push($data,$parentRecursive->id);
            return $this->levelRecursive($parentRecursive->parentRecursive,$data);
        }
        return $data;
    }


    public function getSupervisorByDesignationId($id,$user_id=null){
        $designations = Designation::with('level.parentRecursive')->find($id);
        $data[] = $designations->level->id;
        $dataRecursive = $this->levelRecursive($designations->level->parentRecursive,$data);
        // $supervisor = Designation::with('user')->whereIn('level_id',$dataRecursive)->get();

        $supervisor = User::select('users.*',\DB::raw('CONCAT(users.first_name," ",users.last_name) as fullname'),'designations.designation_name','levels.level_name');

            if(!empty($user_id)){$supervisor->where('users.id','!=',$user_id);}

            return $supervisor->join('designations','designations.id','=','users.designation_id')
                        ->join('levels','levels.id','=','designations.level_id')
                        ->whereIn('designations.level_id',$dataRecursive)->get();             
        // dd($supervisor);
    }


    // public function getSupervisorByDesignationId($id){
        // $designations = Designation::with([
        //     'level.designation.user' => function($query){$query->where('users.id','!=',Auth::guard('hrms')->user()->id);},
        //     'level.parent.designation.user' => function($query){$query->where('users.id','!=',Auth::guard('hrms')->user()->id);}
        //     ])->find($id);

        // $designations = Designation::with('level.designation.user','level.parent.designation.user')->find($id);

        // $designations = Designation::with('level.parentRecursive')->find($id);
        // dd($designations);
        // dd($designations->level->id);

        // $data = [];
        // $data[] = $designations->level->id;
        // dd($data);
        // $dataRecursive = $this->levelRecursive($designations->level->parentRecursive,$data);

        // dd($dataRecursive);

        // foreach($designations->level->designation as $dinfo){
        //     $level_name = $dinfo->level->level_name;
        //     $department_name = $dinfo->department->department_name;

        //     foreach($dinfo->user as $uinfo){
        //         $data[] = [
        //             'designation_name' => $dinfo->designation_name,
        //             'level_name' => $level_name,
        //             'department_name' => $department_name,
        //             'fullname' => $uinfo->fullname,
        //             'employee_no' => $uinfo->employee_no,
        //             'user_id' => $uinfo->id,
        //         ];
        //     }
        // }

        // if($designations->level->parent){
        //     foreach($designations->level->parent->designation as $dinfo){
        //         $level_name = $dinfo->level->level_name;
        //         $department_name = $dinfo->department->department_name;

        //         foreach($dinfo->user as $uinfo){
        //             $data[] = [
        //                 'designation_name' => $dinfo->designation_name,
        //                 'level_name' => $level_name,
        //                 'department_name' => $department_name,
        //                 'fullname' => $uinfo->fullname,
        //                 'employee_no' => $uinfo->employee_no,
        //                 'user_id' => $uinfo->id,
        //             ];
        //         }
        //     }
        // }

        // return response()->json($data);

        // dd($data);
        // dd($designations);
    // }


	public function getDivisions(){
		return Division::get();
	}


    public function getDistrictByDivision($division_id){
        return District::where('division_id',$division_id)->get();
    }


	public function getPolicStationByDistrict($district_id){
		return PoliceStation::where('district_id',$district_id)->get();
	}


    public function getBloodGroups(){
    	return BloodGroup::get();
    }


    public function getEducationLevels(){
    	return EducationLevel::get();
    }


    public function getInstituteByEducationLevel($education_level_id){
    	return Institute::where('education_level_id',$education_level_id)->get();
    }


    public function getDegreeByEducationLevel($education_level_id){
        return Degree::where('education_level_id',$education_level_id)->get();
    }


    public function getBanks(){
        return Bank::where('status',1)->get();
    }


    public function getAllowances(){
        return BasicSalaryInfo::all();
    }


    public function getLevelSalaryInfoByUser($user_id){
        return User::with('designation.level.salaryInfo.basicSalaryInfo')->find($user_id);
    }


    public function getAllowanceNotinLevel($ids=null){

        $basicSalaryInfo =  BasicSalaryInfo::select('basic_salary_info.*')
            ->leftJoin('level_salary_info_map',function($q){
                $q->on('level_salary_info_map.basic_salary_info_id','=','basic_salary_info.id')
                ->where('level_salary_info_map.level_id','=',null);
            })
            
            ->leftJoin('employee_salaries',function($q){
                $q->on('employee_salaries.basic_salary_info_id','=','basic_salary_info.id')
                    ->where('employee_salaries.basic_salary_info_id','=',null);
            });
            
            if($ids !=null){
                $ids = explode(',',$ids);
                $basicSalaryInfo->whereNotIn('basic_salary_info.id',$ids);
            }
        return $basicSalaryInfo->get();
    }


    public function getAllowanceByIds($ids){
        $ids = explode(',',$ids);
        return BasicSalaryInfo::whereIn('id',$ids)->get();
    }


    public function getLanguage(){
        return Language::where('status',1)->get();
    }
    

    public function getReligions(){
        return Religion::all();
    }


    public function getSisterConcern($config_id){
        Artisan::call('db:connect');
        return Config::find($config_id)->sister()->get();
    }


    public function getMotherConcern($config_id){
        Artisan::call('db:connect');
        return Config::find($config_id)->mother()->get();
    }


    public function getWorkShift($id=null,$status=null){

        if((!empty($id) && $id!='null') && $status !=null){
            return WorkShift::where('id',$id)->where('work_shift_status',$status)->get();
        }else if(!empty($id) && $id!='null'){
            return WorkShift::find($id);
        }else if($status){
            return WorkShift::where('work_shift_status',$status)->get();
        }else{
            return WorkShift::all();
        }
    }

    public function userTakenLeave($id){

        $currentYear = date('Y');
        $data_map = UserLeaveTypeMap::with('leaveType')->where('user_id', $id)
                    ->where('active_from_year', '<=', $currentYear)
                    ->where('active_to_year', '>=', $currentYear)->where('status', 1)->get();

        $leave_amount_ary = [];   //leave total amount

        $sl = 0;
        foreach($data_map as $mapInfo){
            $leave_amount_ary[$sl]['id'] = $mapInfo->leave_type_id;
            $leave_amount_ary[$sl]['name'] = $mapInfo->leaveType->leave_type_name;
            $leave_amount_ary[$sl]['days'] = $mapInfo->number_of_days;
            $sl++;
        }

        $data_val = EmployeeLeave::with('leaveType')->where('user_id', $id)->whereIn('employee_leave_status', [1,2,3])->whereYear('employee_leave_from', $currentYear)->get();

        $leave_type_id_ary = [];
        $leave_type_name_ary = [];
        $leave_type_days_ary = [];
        $taken_leave_ary = [];

        //calculate which type leave taken how many days...
        foreach($data_val as $info){
            
            $diff_days = $info->employee_leave_total_days;

            if(!in_array($info->leave_type_id, $leave_type_id_ary)){
                $leave_type_id_ary[] = $info->leave_type_id;
                $leave_type_name_ary[] = $info->leaveType->leave_type_name;
                $leave_type_days_ary[] = $diff_days;
            }
            else{
                $location = array_search($info->leave_type_id, $leave_type_id_ary);
                $old_days = $leave_type_days_ary[$location];
                $leave_type_days_ary[$location] = $diff_days + $old_days;
            }
        }

        if(count($leave_type_id_ary) > 0){
            for($i=0 ; $i<count($leave_type_id_ary) ; $i++){
                $taken_leave_ary[$i]['id'] = $leave_type_id_ary[$i];
                $taken_leave_ary[$i]['name'] = $leave_type_name_ary[$i];
                $taken_leave_ary[$i]['days'] = $leave_type_days_ary[$i];
            }
        }

        //combined leave amount and taken leave to show leave history
        $leave_amount_taken_combination = [];
        $sl = 0;
        foreach($leave_amount_ary as $info){
            $leave_amount_taken_combination[$sl]['id'] = $info['id'];
            $leave_amount_taken_combination[$sl]['name'] = $info['name'];
            $leave_amount_taken_combination[$sl]['amount_days'] = $info['days'];
            
            if(in_array($info['id'], $leave_type_id_ary)){
                $locId = array_search($info['id'], $leave_type_id_ary);
                $leave_amount_taken_combination[$sl]['taken_days'] = $taken_leave_ary[$locId]['days'];
                $balanceLeave = $info['days'] - $taken_leave_ary[$locId]['days'];
                $leave_amount_taken_combination[$sl]['balance'] = $balanceLeave < 0 ? 0 : $balanceLeave;
            }
            else{
                $leave_amount_taken_combination[$sl]['taken_days'] = 0;
                $leave_amount_taken_combination[$sl]['balance'] = $info['days'];
            }

            $sl++;
        }

        //calculate remain leave to show leave type
        $leave_type_ary = [];
        $aryIndex = 0;
        foreach($leave_amount_ary as $amountInfo){

            if(in_array($amountInfo['id'], $leave_type_id_ary)){
                //find leave taken days using id from taken leave ary days
                $indexId = array_search($amountInfo['id'], $leave_type_id_ary);
                $leave_type_ary[$aryIndex]['id'] =  $amountInfo['id'];
                $leave_type_ary[$aryIndex]['name'] =  $leave_type_name_ary[$indexId];
                $leave_type_ary[$aryIndex]['days'] =  $amountInfo['days']-$leave_type_days_ary[$indexId];
            }
            else{
                $leave_type_ary[$aryIndex]['id'] =  $amountInfo['id'];
                $leave_type_ary[$aryIndex]['name'] =  $amountInfo['name'];
                $leave_type_ary[$aryIndex]['days'] =  $amountInfo['days'];
            }

            $aryIndex++;
        }
        
        $data['userHaveLeavs'] = $data_map;
        $data['taken_leave_type_id'] = $leave_type_id_ary;
        $data['taken_leave_type_name'] = $leave_type_name_ary;
        $data['taken_leave_type_days'] = $leave_type_days_ary;
        $data['taken_leave_ary'] = $taken_leave_ary; //leave taken
        $data['user_leave_type'] = $leave_type_ary; //leave remain
        $data['show_history'] = $leave_amount_taken_combination;

        session()->put('global_leave_type_ary', $leave_type_ary);

        return $data;
    }

    public function showIndiReport($id){

        $data['title'] = "Leave|Individual Report";
        $data['info'] = EmployeeLeave::with('leaveType', 'userName.designation.department', 'responsibleUser.designation.department', 'approvedByUser.designation.department', 'supervisorUser.designation.department', 'forwardUser.designation.department')->find($id);

        return view('leave.showIndiReport', $data);
    }

    public function getBonusType(){
        return BonusType::where('bonus_type_status',1)->orderBy('id','desc')->get();
    }


    public function getEmployees(){
        return User::select('users.*',DB::raw('CONCAT(users.first_name," ",users.last_name) as fullname'),'designations.designation_name','levels.level_name')
            ->where('users.status',1)
            ->join('designations','designations.id','=','users.designation_id')
            ->join('levels','levels.id','=','designations.level_id')
            ->get();
    }


    public function getEmployeeByDesignationId($id){
        return User::select('users.*',DB::raw('CONCAT(users.first_name," ",users.last_name) as fullname'),'designations.designation_name','levels.level_name')
            ->where('users.status',1)->where('users.designation_id',$id)
            ->join('designations','designations.id','=','users.designation_id')
            ->join('levels','levels.id','=','designations.level_id')
            ->get();
    }


    public function getIncrementType(){
        return IncrementType::where('increment_type_status',1)->orderBy('id','desc')->get();
    }


    public function getLoanType(){
        return LoanType::where('loan_type_status',1)->orderBy('id','desc')->get();
    }


    public function getEmployeeByDepartmentUnitBranch($branch_id, $department_id, $unit_id)
    {
        $result = User::select('users.*', DB::raw('CONCAT(users.first_name," ",users.last_name) as fullname'));

        if($branch_id !=0){
            $result->where('branch_id', $branch_id);
        }

        if($unit_id !=0){
            $result->where('unit_id', $unit_id);
        }

        if($department_id !=0){
            $result->where('departments.id', $department_id)
                ->join('units','units.id','=','users.unit_id')
                ->join('departments','departments.id','=','units.unit_departments_id');
        }

        return $result->get();
    }


    public function calculateGrossSalary($user_id, $effective_date)
    {
        $user = User::with(['salaries'=>function($q)use($user_id, $effective_date){
            $q->where('user_id', $user_id)
                ->where('salary_effective_date','<=',$effective_date);
            },'salaries.basicSalaryInfo'])
            ->find($user_id);

        if(count($user) <= 0){
            return 0.00;
        }elseif(count($user->salaries) <= 0){
            $gross_salary = $user->basic_salary + $user->salary_in_cache;
            return $gross_salary;
        }

        $allowance_deduction = $user->salaries;
        $basic_salary = $user->basic_salary;
        $salary_in_cash = $user->salary_in_cache;
        $total_allowance = 0;

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
            }
        }

        $gross_salary = $user->basic_salary + $user->salary_in_cache + $total_allowance;
        return $gross_salary;
    }




}
