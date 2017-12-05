<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Level;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Designation;
use App\Models\Units;
use App\Models\EmployeeType;

use App\Services\CommonService;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;

class SwitchAccountController extends Controller
{
	use CommonService;

	protected $auth;

	protected $user;

    public function __construct(User $user){
    	$this->middleware('auth:hrms');

	 	$this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });

        $this->user = $user;
    }


    public function switchAccount($database_name,$config_id){

        Artisan::call('db:connect',['database' => $database_name]);
    	$check_user_exists = User::where('email',$this->auth->email)->first();

    	if($check_user_exists){
    		$this->switchAccountLogin($database_name, $config_id, $check_user_exists->id);
    	}else{
    		$this->switchAccountRegister($database_name, $config_id);
    	}
    	return redirect()->back();

    }


    private function switchAccountLogin($database_name, $config_id, $user_id){
    	Artisan::call('db:connect',['database' => $database_name]);

    	if(Auth::guard('hrms')->loginUsingId($user_id)){
    		Session(['database'=>$database_name, 'config_id' => $config_id]);
    		$this->settings();
    		Session::flash('success','Account successfully switch.');
    	}else{
    		Session::flash('danger','Account not switch.');
    	}
    	return redirect()->back();
    }


    private function switchAccountRegister($database_name, $config_id){

    	Artisan::call('db:connect',['database' => Session('database')]);

		$employee_type_id = $this->getEmployeeType();
    	$branch_id = $this->getBranch();
    	$designation_id = $this->getDesignation();
    	$unit_id = $this->getUnit($designation_id);

    	Artisan::call('db:connect',['database' => $database_name]);

    	try{
	    	if($user = User::create([
			    "employee_no" => $this->auth->employee_no,
			    "employee_type_id" => $employee_type_id,
			    "branch_id" => $branch_id,
			    "designation_id" => $designation_id,
			    "unit_id" => $unit_id,
			    "basic_salary" => $this->auth->basic_salary,
			    "salary_in_cache" => $this->auth->salary_in_cache,
			    "effective_date" => $this->auth->effective_date,
			    "first_name" => $this->auth->first_name,
			    "middle_name" => $this->auth->middle_name,
			    "last_name" => $this->auth->last_name,
			    "nick_name" => $this->auth->nick_name,
			    "email" => $this->auth->email,
			    "password" => $this->auth->password,
			    "status" => 55,
			    "mobile_number" => $this->auth->mobile_number,
			    "photo" => $this->auth->photo,
	    		]))
	    	{
                //copy photo
                if(!Storage::disk('public')->exists(Session('config_id').'/'.$user->id.'/'.$this->auth->photo)){
                    Storage::disk('public')->copy(Session('config_id').'/'.$this->auth->id.'/'.$this->auth->photo, $config_id.'/'.$user->id.'/'.$this->auth->photo);
                }
	    		$this->switchAccountLogin($database_name, $config_id,$user->id);
	    	}

	    }catch(\Exception $e){
	    	$code = $e->getCode();

	    	// if($code == '23000'){
	    	// 	$message  = $e->getMessage();

	    	// 	if(stristr($message,'REFERENCES')){
	    	// 		$msgData = explode(' ', $message);
	    	// 		$reference_key = array_search('REFERENCES',$msgData);
	    	// 		$table =  str_replace('`','',$msgData[$reference_key+1]);
	    	// 	}

	    	// 	if(isset($table)){
	    	// 		Artisan::call('db:connect',['database' => Session('database')]);
	    	// 		$this->setDependanceyData($table);
	    	// 	}
	    	// }

            Artisan::call('db:connect',['database' => Session('database')]);
            Session::flash('danger','Account not switch.Try again.');
            return redirect()->back();
	    }
    }


    private function setDependanceyData($table){
    	if($table == 'employee_types'){
    		$this->getEmployeeType();
		}

		if($table == 'designations'){
			$this->getDesignation();
		}

		if($table == 'branchs'){
			$this->getBranch();
		}

		if($table == 'units'){
			$this->getUnit();
		}

		// $data['employee_type_id'] = $this->getEmployeeType();
  //   	$data['branch_id'] = $this->getBranch();
  //   	$data['designation_id'] = $this->getDesignation();
  //   	$data['unit_id'] = $this->getUnit($data['designation_id']);
    }


    private function getEmployeeType(){

    	if(!EmployeeType::find($this->auth->employee_type_id)){
	    	$employee_type = EmployeeType::orderBy('id','asc')->first();
	    	$employee_type_id = $employee_type->id;
	    }else{
	    	$employee_type_id = $this->auth->employee_type_id;
	    }
	    return $employee_type_id;
    }


    private function getBranch(){

    	if(!Branch::find($this->auth->branch_id)){
    		$branch = Branch::orderBy('id','asc')->first();
    		$branch_id = $branch->id;
		}else{
			$branch_id = $this->auth->branch_id;
		}
		return $branch_id;
    }


    // private function getLevel(){

    // 	if(!Level::find($this->auth->designation->level_id)){
    // 		$level = Level::orderBy('id','asc')->first();
    // 		$level_id = $level->id;
    // 	}else{
    // 		$level_id = $this->auth->designation->level_id;
    // 	}
    // 	return $level_id;
    // }


    private function getDepartment($designation_id){
    	if(!Department::find($designation_id)){
    		$department = Department::orderBy('id','asc')->first();
    		$department_id = $department->id;
    	}else{
    		$department_id = $designation_id;
    	}
    	return $department_id;
    }


    private function getUnit($designation_id){
    	$department_id = $this->getDepartment($designation_id);

    	if(Units::where('unit_departments_id',$department_id)->count() <=0 ){
    		$unit = Units::orderBy('id','asc')->first();
    		$unit_id = $unit->id;
    	}else{
    		$unit_id = $department_id;
    	}
    	return $unit_id;
    }


    private function getDesignation(){
    	
    	if(!Designation::find($this->auth->designation_id)){
	    	$designation = Designation::orderBy('id','asc')->first();
    		$designation_id = $designation->id;
	    }else{
	    	$designation_id = $this->auth->designation->id;
	    }

	    return $designation_id;
    }





}
