<?php
namespace App\Http\Controllers\Pim;

use App\Models\Setup\UserEmails;

use App\Models\User;
use App\Models\UserEmployeeTypeMap;
use App\Models\EmployeeDetail;
use App\Models\EmployeeAddress;
use App\Models\EmployeeEducation;
use App\Models\EmployeeExperience;
use App\Models\EmployeeSalary;
use App\Models\EmployeeNominee;
use App\Models\EmployeeTraining;
use App\Models\EmployeeReference;
use App\Models\EmployeeChildren;
use App\Models\EmployeeLanguage;
use App\Models\EmployeeSalaryAccount;
use App\Models\Designation;
use App\Models\LevelPermission;
use App\Models\UserPermission;
use App\Models\Module;
use App\Models\LeaveType;
use App\Models\UserLeaveTypeMap;
use App\Models\EmployeeStatus;
use App\Models\EmployeeType;

use App\Models\UserEmployeeStatusMap; //will be removed
use App\Models\EmpTypeMapWithEmpStatus;

use App\Services\CommonService;
use App\Services\PermissionService;

use App\Jobs\UserEmailUpdate;
use App\Jobs\CalculateEarnLeaveJob;

use App\Http\Requests\EmployeeBasicInfoRequest;
use App\Http\Requests\EmployeePersonalInfoRequest;
use App\Http\Requests\EmployeeEducationRequest;
use App\Http\Requests\EmployeeExperienceRequest;
use App\Http\Requests\EmployeeSalaryRequest;
use App\Http\Requests\EmployeeTrainingRequest;
use App\Http\Requests\EmployeeNomineeRequest;
use App\Http\Requests\EmployeeReferenceRequest;
use App\Http\Requests\EmployeeChildrenRequest;
use App\Http\Requests\EmployeeLanguageRequest;


use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    use CommonService, PermissionService;

    protected $auth;

    /**
     * EmployeeController constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth,User $user)
    {
        $this->middleware('auth:hrms');
        $this->middleware('CheckPermissions', ['except' => ['viewEmployeeProfile', 'statusChange', 'permission', 'updatePermission', 'leave', 'updateLeave', 'employeeStatus', 'updateEmployeeStatus', 'getEmployeeStatus', 'testJobEmpStatus', 'updateEmpType', 'getEmployeeTypesHistory', 'deleteUpComming']]);

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });

        $this->user = $user;
    }

    /**
     * @get Show Employee list
     * @return $this
     */
    public function index(){

        $data['title'] = 'Employee List';
        $data['users'] = User::with('designation','createdBy','updatedBy')->orderBy('status')->get();
        $data['modules_permission'] = Module::with('menus','menus.child_menu')->where('module_status', 1)->get();
        $data['sidebar_hide'] = true;
        $data['leave_types'] = LeaveType::where('leave_type_status', 1)->get();
        $data['allStatus'] = EmployeeStatus::where('status', 1)->get();
        $data['empTypes'] = EmployeeType::where('status', 1)->get();

        return view('pim.employee.index')->with($data);
    }

    public function permission($id){

        $data['users_per'] = UserPermission::where('user_id', $id)->get();

        return $data['users_per'];
    }

    public function updatePermission(Request $request){

        $this->validate($request, [
            'hdn_id' => 'required'
        ]);
        
        DB::beginTransaction();

        try {
            foreach($request->user_menus as $key=>$value){
                if($value == 0){
                    $uncheckedAray[] = $key;
                }
                else{
                    $checkedAray[] = $key;    
                }
            }

            if(!empty($uncheckedAray)){
                UserPermission::where('user_id', $request->hdn_id)
                        ->whereIn('menu_id', $uncheckedAray)->delete();
            }

            if(!empty($checkedAray)){
                $exist_menu_obj = UserPermission::select('menu_id')->where('user_id', $request->hdn_id)
                        ->whereIn('menu_id', $checkedAray)->get()->toArray();
            }

            $exist_menu_ary = array_column($exist_menu_obj, 'menu_id');
            $aryDiff = array_diff($checkedAray,$exist_menu_ary);

            if(!empty($aryDiff)){
                foreach($aryDiff as $info){
                    $user_permission[] = [
                                'user_id' => $request->hdn_id,
                                'menu_id' => $info
                            ];
                }

                UserPermission::insert($user_permission);
            }

            DB::commit();
            $request->session()->flash('success','Data successfully updatsed!');

            //hrmsSideBar from PermissionService
            //update Session Data After update Permission
            $this->hrmsSideBar();
            $this->userPermission(\Auth::user()->id);

        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('danger','Data not updated!');
        }

        return redirect('employee/index');
    }

    public function getEmployeeStatus($id){

        $data = json_decode(json_encode($this->getEmployeeTypesHistory($id)));

        //count this employee's emp type history
        $historyCount = count($data->original->history);

        //Only one upcoming or future type can be added for a emp.

        if($data->original->up_coming_type == 0){
            //no upComing emp type
            //so history last row is the current or final emp type
            $finalOrCurrentType = $data->original->history[$historyCount-1];
        }
        else{
            //this emp hav upComing emp type
            //so current type is last row's prev row
            $finalOrCurrentType = $data->original->history[$historyCount-2];
        }

        $current_type_map_id = $finalOrCurrentType->id;
        $checking = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $current_type_map_id)->first();

        //If no data in emp_type_map_with_emp_status it generate data
        if(count($checking) == 0){

            $emp_type_data = UserEmployeeTypeMap::where('id', $current_type_map_id)->first();

            DB::beginTransaction();

            try {

                $sav = new EmpTypeMapWithEmpStatus;
                $sav->user_emp_type_map_id = $current_type_map_id;
                $sav->employee_status_id = 1;
                $sav->from_date = $emp_type_data->from_date;
                $sav->remarks = "Generated By System";
                $sav->created_by = Auth::user()->id;
                $sav->save();
            
                $savUser = User::find($emp_type_data->user_id)->update(['status' => 1]);

                DB::commit();
            } catch (\Exception $e) {
                
                DB::rollback();
            }
        }

        $current_types_status_history = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $current_type_map_id)->get();

        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $current_date = $date->format('Y-m-d');
        
        //######### find upcomming status --Start ########
        //only one future data canbe save so only last one will be upcomming or not
        $upCommingStatus = 0;
        $lastIndex = count($current_types_status_history);

        if($lastIndex > 0){

            $tempAry = $current_types_status_history[$lastIndex-1];

            if(strtotime($tempAry->from_date) > strtotime($current_date)){

                $upCommingStatus = $tempAry->id;
            }
        }
        //######### find upcomming status --Finished

        //@@******* Check emp Valid or Not **********
        $validityStatus = "Valid";

        if(!empty($finalOrCurrentType->to_date) && (strtotime($finalOrCurrentType->to_date) < strtotime($current_date))) {
            $validityStatus = "Invalid";
        }

        $dataAry['final_or_current_type'] = $finalOrCurrentType;
        $dataAry['validity'] = $validityStatus;
        $dataAry['status_history'] = $current_types_status_history;
        $dataAry['upcomming_status'] = $upCommingStatus;

        return $dataAry;
    }

    public function updateEmployeeStatus(Request $request){

        $this->validate($request, [
            'status_name' => 'required',
            'user_emp_type_map_id' => 'required',
            'status_eff_date' => 'required|after:now',
        ],[
            'status_eff_date.required' => 'Status effective date is required.',
        ]);

        $status_name = $request->status_name;
        $user_emp_type_map_id = $request->user_emp_type_map_id;
        $to_date_limit = $request->to_date_limit;

        $status_eff_date = $request->status_eff_date;
        $fromDateAry = explode('-', $status_eff_date);

        $prev_date = date('Y-m-d', strtotime($status_eff_date .' -1 day'));

        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $current_date = $date->format('Y-m-d');

        DB::beginTransaction();

        $oldData = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $user_emp_type_map_id)->orderBy('id', 'DESC')->take(2)->get();

        try{

            if( (strtotime($status_eff_date) <= strtotime($to_date_limit)) || empty($to_date_limit)) {
                
                if(strtotime($oldData[0]->from_date) <= strtotime($current_date)){
                    
                    $oldData[0]->to_date = $prev_date;
                    $oldData[0]->save();     
                }
                else{

                    if(count($oldData[1]) > 0){

                        $oldData[1]->to_date = $prev_date;
                        $oldData[1]->save();
                    }

                    $oldData[0]->delete();
                }

                $sav = new EmpTypeMapWithEmpStatus;
                $sav->user_emp_type_map_id = $user_emp_type_map_id;
                $sav->employee_status_id = $status_name;
                $sav->from_date = $status_eff_date;
                $sav->remarks = $request->status_remarks;
                $sav->created_by = Auth::user()->id;
                $sav->save();
                
                DB::commit();  
                $data['title'] = 'success';
                $data['message'] = 'Status successfully updated !';        
            }
            else{

                $data['title'] = 'warning';
                $data['message'] = 'Effective date must smaller then '. $to_date_limit; 
            }

        }catch (\Exception $e) {
           
           DB::rollback(); 
           $data['title'] = 'error';
           $data['message'] = 'Status not changed.. !';
        }

        return response()->json($data); 

    }

    public function deleteUpComming($id, $typee){

        //using this one function upCommingTYpe
        // and upCommingStatus both will be delete

        if($typee == 'EmpType'){

            $prev_val = UserEmployeeTypeMap::find($id);
            $user_id = $prev_val->user_id;
            $prev_val->delete();

            //only one upcomming type can be set
            //so after delete upcomming type
            //the current efftive type is the last one
            //so if last row emp_type is 1 or 3 just remove to_date

            $data = UserEmployeeTypeMap::where('user_id', $user_id)->orderBy('id','desc')->first();
            if($data->employee_type_id == 1 || $data->employee_type_id == 3){
                $data->to_date = null;
                $data->save();
            }
        }
        else{
            $prev_val = EmpTypeMapWithEmpStatus::find($id);
            $map_id = $prev_val->user_emp_type_map_id;
            $prev_val->delete();

            $data = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $map_id)->orderBy('id','desc')->first();
            $data->to_date = null;
            $data->save();
        }
            

    }

    public function updateEmpType(Request $request){

        $this->validate($request, [
            'type_name' => 'required|numeric',
            'from_date' => 'required|date_format:Y-m-d|after:now',
            'to_date' => 'date_format:Y-m-d|required_if:type_name,2,4|after:from_date',
        ]);
            
        $user_id = $request->user_id;

        $type_map = UserEmployeeTypeMap::where('user_id',$user_id)->orderBy('id','desc')->first();

        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $current_date = $date->format('Y-m-d');

        $eff_date = $request->from_date;
        $eff_to_date = $request->to_date;

        if($request->type_name == 1 || $request->type_name == 3){
            $eff_to_date = null;
        }

        DB::beginTransaction();

        try{
            //if already have upcoming type.. new type not be added
            if(strtotime($current_date) > strtotime($type_map->from_date)){

                //new type will be added
                $save = new UserEmployeeTypeMap;
                $save->user_id = $user_id;
                $save->employee_type_id = $request->type_name;
                $save->from_date = $eff_date;
                $save->to_date = $eff_to_date;
                $save->remarks = $request->remarks;
                $save->created_by = Auth::user()->id;
                $save->save();

                //update previous type to_date
                $type_map->to_date = date('Y-m-d', strtotime($eff_date .' -1 day'));
                $type_map->updated_by = Auth::user()->id;
                $type_map->save();

                DB::commit();
                $data['title'] = 'success';
                $data['message'] = 'New employee type successfully added !';
            }
            else{
                //delete previous type
                UserEmployeeTypeMap::find($type_map->id)->delete();

                $save = new UserEmployeeTypeMap;
                $save->user_id = $user_id;
                $save->employee_type_id = $request->type_name;
                $save->from_date = $eff_date;
                $save->to_date = $eff_to_date;
                $save->remarks = $request->remarks;
                $save->created_by = Auth::user()->id;
                $save->save();

                //update previous type to_date

                $type_map_prev = UserEmployeeTypeMap::where('user_id',$user_id)->orderBy('id','desc')->skip(1)->first();
                $type_map_prev->to_date = date('Y-m-d', strtotime($eff_date .' -1 day'));
                $type_map_prev->updated_by = Auth::user()->id;
                $type_map_prev->save();

                DB::commit();
                $data['title'] = 'success';
                $data['message'] = 'New employee type successfully added...... !';
            }
            
        }catch (\Exception $e) {
           
           DB::rollback(); 
           $data['title'] = 'error';
           $data['message'] = 'Employee type not changed !';
        }

        return response()->json($data);
        
    }

    public function getEmployeeTypesHistory($id){

        $upCommingType = 0;
        $data['history'] = UserEmployeeTypeMap::where('user_id', $id)->get();

        // $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        // $current_date = $date->format('Y-m-d');

        // $findLastIndex = count($data['history'])-1;

        // $lastData = $data['history'][$findLastIndex];

        // if($lastData->employee_type_id == 2 || $lastData->employee_type_id == 4){
        //     if(strtotime($current_date) >= strtotime($lastData->from_date) && strtotime($current_date) <= strtotime($lastData->to_date)){
        //         //this condition because only one future type add possible
        //         $current_type = $lastData->employee_type_id;
        //     }
        //     else{
        //         //find upcomming emp type
        //         $upCommingType = $lastData->id;
        //         //else prev one must be current one
        //         $lastData = $data['history'][$findLastIndex-1];
        //         // $current_type = $lastData->employee_type_id;
        //         if(strtotime($current_date) >= strtotime($lastData->from_date) && strtotime($current_date) <= strtotime($lastData->to_date)){
        //             //this condition because only one future type add possible
        //             $current_type = $lastData->employee_type_id;
        //         }
        //         else{
        //             $current_type = "Invalid";
        //         }
        //     }
        // }
        // else{
        //     if(empty($lastData->to_date)){
        //         if(strtotime($current_date) >= strtotime($lastData->from_date)){
        //            $current_type = $lastData->employee_type_id; 
        //         }
        //         else{
        //             //find upcomming emp type
        //             $upCommingType = $lastData->id;
                    
        //             //else prev one must be current one
        //             $lastData = $data['history'][$findLastIndex-1];
        //             $current_type = $lastData->employee_type_id;
        //         }
        //     }else{
        //         if(strtotime($current_date) >= strtotime($lastData->from_date) && strtotime($current_date) <= strtotime($lastData->to_date)){
        //             //this condition because only one future type add possible
        //             $current_type = $lastData->employee_type_id;
        //         }
        //         else{
        //             $current_type = "Invalid";
        //         }
        //     }
        // }


            $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
            $currentDate = $date->format('Y-m-d');

            $dataa = UserEmployeeTypeMap::where('user_id', $id)->orderBy('id', 'DESC')->take(2)->get();

            if(count($dataa) == 1){
                // dd($dataa);
                $current_type = $dataa[0]->employee_type_id;
            }
            else{
                if(empty($dataa[0]->to_date) && (strtotime($dataa[0]->from_date) <= strtotime($currentDate))){

                    // dd($dataa[0]);
                    $current_type = $dataa[0]->employee_type_id;
                }
                elseif((strtotime($dataa[0]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[0]->to_date) >= strtotime($currentDate)) && !empty($dataa[0]->to_date)){

                    // dd($dataa[0]);
                    $current_type = $dataa[0]->employee_type_id;
                }
                elseif((strtotime($dataa[1]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[1]->to_date) >= strtotime($currentDate)) && !empty($dataa[1]->to_date)){

                    // dd($dataa[1]);
                    $current_type = $dataa[1]->employee_type_id;
                    $upCommingType = $dataa[0]->id;
                }
                else{
                    // echo "Invalid";
                    // dd($dataa[0]);
                    $current_type = $dataa[0]->employee_type_id;
                }
            }



        $data['current_type'] = $current_type;
        $data['up_coming_type'] = $upCommingType; //upComming type can be delete
        $data['emp_types'] = EmployeeType::where('status', 1)->get();


        return response()->json($data); 
    }

    /**
     * @get Show Employee Profile
     * @param null $employee_no
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function viewEmployeeProfile($employee_no=null){

        $sidebar_hide = true;
        if(!empty($employee_no)){
            $user = $this->user->get_profile_info($employee_no);
            if(!$user){
                return redirect()->back();
            }
        }else{
            $user = $this->user->get_profile_info($this->auth->id);
        }

        return view('pim.employee.view',compact('user','sidebar_hide'));
    }


    /**
     * @get Show Add Employee Form
     * @param Request $request
     * @return $this
     */
    public function showEmployeeAddForm(Request $request){

        $data['sidebar_hide'] = true;
        $data['tab'] = $request->tab;

        if($user = User::find($request->id)){
            $data['user'] = $user;
            $data['id'] = $user->id;

            if($request->ajax()){
                return $this->user->get_user_data_by_user_tab($user->id, $request->tab, 'add');
            }
        }else{
            if($request->ajax()){
                return response()->json([]);
            }

            $employee_no = User::orderBy('id','desc')->first();
            $data['next_employee_id'] = $employee_no->next_employee_no;
        }

        return view('pim.employee.add')->with($data);
    }


    /**
     * Create Employee Account with Basic Information
     * @param EmployeeBasicInfoRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */

    public function addEmployee(EmployeeBasicInfoRequest $request){
        $request->offsetSet('password', bcrypt($request->password));
        $request->offsetSet('created_by',$this->auth->id);
        try{
            Artisan::call('db:connect');
            if(UserEmails::where('email',$request->email)->count() <= 0){
                UserEmails::create([
                    'config_id' => Session('config_id'),
                    'email' => $request->email,
                ]);
            }else{
               
                if($request->ajax()){
                    $data['status'] = 'warning';
                    $data['statusType'] = 'NotOk';
                    $data['code'] = 500;
                    $data['type'] = null;
                    $data['title'] = 'Error!';
                    $data['message'] = 'Employee Email Already Exits!';
                    return response()->json($data,500);
                }else{
                    $request->session()->flash('warning','Employee Email Already Exits!');
                    return redirect()->back()->withInput();
                }
            }
            Artisan::call("db:connect", ['database' => Session('database')]);
            DB::beginTransaction();
            if($request->hasFile('image')){
                $photo = time().'.'.$request->image->extension();
                $request->offsetSet('photo',$photo);
            }
            $user = User::create($request->all());
            //@@**Insert Data Into Leave & Permission
            $this->insertLeavePermission($user, $request->designation_id, $request->employee_type_id);
            if($user){
                if(isset($photo)){
                    if(!$request->image->storeAs(Session('config_id').'/'.$user->id,$photo)){
                        $request->session()->flash('warning','Photo Not Upload.Update photo form edit.');
                    }
                }
            }
            $request->offsetSet('user_id',$user->id);
            EmployeeAddress::create($request->all());
            
            $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
            $current_date = $date->format('Y-m-d');
            $createUserEmpType = UserEmployeeTypeMap::create($request->all());
            //@@**Insert Data Into Emp Status
            //@@**for future emp type Status data will be not inserted 
            if(strtotime($request->from_date) <= strtotime($current_date)) {
                EmpTypeMapWithEmpStatus::create([
                    'user_emp_type_map_id' => $createUserEmpType->id,
                    'employee_status_id' => 1,
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'remarks' => "Generated when employee add.",
                    'created_by' => Auth::user()->id,
                ]);
            }
            
            //@@**Just updating EarnLeaveJOb
            dispatch(new CalculateEarnLeaveJob());
            // echo "ttss";
            // var_dump($request->all());
            // die();
            DB::commit();
            if($request->ajax()){
                $userData = $this->user->get_user_data_by_user_tab($user->id, $request->tab);
                $data['data'] = $userData->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_next'))?'personal':null;
                $data['title'] = 'Success!';
                $data['message'] = 'Employee Successfully Added!';
                return response()->json($data,200);
            }
            $request->session()->flash('success','Employee Successfully Added!');
            if($request->has('save_next')){
                return redirect('/employee/add/'.$user->id.'/personal');
            }
            return redirect('/employee/add/'.$user->id);
        }catch(\Exception $e){
            DB::rollback();
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Personal Info Not Saved.';
                return response()->json($data,500);
            }
            $request->session()->flash('danger','Employee Not Added!');
            return redirect()->back()->withInput();
        }
    }

    public function insertLeavePermission($user, $designation_id, $employee_type_id){

        //insert menus into user_permisson when user created
        $desig_info = Designation::find($designation_id);
        $level_id = $desig_info->level_id;
        $level_permission = LevelPermission::where('level_id', $level_id)->get();

        foreach($level_permission as $info){
            $user_permission[] = [
                'user_id' => $user->id,
                'menu_id' => $info->menu_id,
            ];
        }

        if(!empty($user_permission)){
            UserPermission::insert($user_permission);
        }
        //end insert menu user_permission    
        
        //insert leave info depend on emp type start
        $emp_type = $employee_type_id; 
        $commonTypeId = [];

        $leaveTypes = LeaveType::where('leave_type_status', 1)->get();

        if(count($leaveTypes) > 0){
            
            foreach($leaveTypes as $val){
                
                $leaveTypeAry = explode(',', $val->leave_type_effective_for);

                if($val->leave_type_is_earn_leave == 1){
                    $num_of_days = 0;
                }
                else{
                    $num_of_days = $val->leave_type_number_of_days; 
                }

                if(in_array($emp_type, $leaveTypeAry)){
                    $commonTypeId['type_id'][] = $val->id;
                    $commonTypeId['days'][] = $num_of_days;
                    $commonTypeId['from_year'][] = $val->leave_type_active_from_year;
                    $commonTypeId['to_year'][] = $val->leave_type_active_to_year;
                }
            }

            $length = count($commonTypeId['type_id']);

            if(!empty($length)){
                for($i=0 ; $i < $length; $i++){
                    $user_leave_type[] = [
                        'user_id' => $user->id,
                        'leave_type_id' => $commonTypeId['type_id'][$i],
                        'number_of_days' => $commonTypeId['days'][$i],
                        'active_from_year' => $commonTypeId['from_year'][$i],
                        'active_to_year' => $commonTypeId['to_year'][$i],
                        'status' => 1,
                    ];
                }
            }

            if(!empty($user_leave_type)){
                UserLeaveTypeMap::insert($user_leave_type);
            }
        }
        //leave end
    }

    public function insertLeavePermissionEdit($user, $designation_id, $employee_type_id, $oldEmpType){

        //insert menus into user_permisson when user created
        $desig_info = Designation::find($designation_id);
        $level_id = $desig_info->level_id;
        $level_permission = LevelPermission::where('level_id', $level_id)->get();

        foreach($level_permission as $info){
            $user_permission[] = [
                'user_id' => $user->id,
                'menu_id' => $info->menu_id,
            ];
        }

        if(!empty($user_permission)){
            UserPermission::insert($user_permission);
        }
        //end insert menu user_permission    
        
        //remove leave types of old emp type start

        $leaveTypesAvailable = [];

        $types = LeaveType::where('leave_type_status', 1)->get();
        
        foreach($types as $info){
            $types = explode(",", $info->leave_type_effective_for);

            if(in_array($oldEmpType, $types)){
                $leaveTypesAvailable[] = $info->id;
            }
        }

        if(count($leaveTypesAvailable) > 0){
            $pp = UserLeaveTypeMap::where('user_id', $user->id)->whereIn('leave_type_id', $leaveTypesAvailable)->delete();
        }

        //remove leave types end

        //insert leave info depend on emp type start
        $emp_type = $employee_type_id; 
        $commonTypeId = [];

        $leaveTypes = LeaveType::where('leave_type_status', 1)->get();

        if(count($leaveTypes) > 0){
            
            foreach($leaveTypes as $val){
                
                $leaveTypeAry = explode(',', $val->leave_type_effective_for);

                if($val->leave_type_is_earn_leave == 1){
                    $num_of_days = 0;
                }
                else{
                    $num_of_days = $val->leave_type_number_of_days; 
                }

                if(in_array($emp_type, $leaveTypeAry)){
                    $commonTypeId['type_id'][] = $val->id;
                    $commonTypeId['days'][] = $num_of_days;
                    $commonTypeId['from_year'][] = $val->leave_type_active_from_year;
                    $commonTypeId['to_year'][] = $val->leave_type_active_to_year;
                }
            }

            $length = count($commonTypeId['type_id']);

            if(!empty($length)){
                for($i=0 ; $i < $length; $i++){
                    $user_leave_type[] = [
                        'user_id' => $user->id,
                        'leave_type_id' => $commonTypeId['type_id'][$i],
                        'number_of_days' => $commonTypeId['days'][$i],
                        'active_from_year' => $commonTypeId['from_year'][$i],
                        'active_to_year' => $commonTypeId['to_year'][$i],
                        'status' => 1,
                    ];
                }
            }

            if(!empty($user_leave_type)){
                UserLeaveTypeMap::insert($user_leave_type);
            }
        }
        //leave end
    
    }

    /**
     * @post Add Employee Personal Info
     * @param EmployeePersonalInfoRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addPersonalInfo(EmployeePersonalInfoRequest $request){
       try {
            $request->offsetSet('created_by',$this->auth->id);

            if(EmployeeDetail::create($request->all())){
                $data['data'] = User::with('details.bloodGroup')->find($request->userId);
            }

           if($request->ajax()){
               $data['status'] = 'success';
               $data['statusType'] = 'OK';
               $data['code'] = 200;
               $data['type'] = ($request->has('save_personal_and_next'))?'education':null;
               $data['title'] = 'Success!';
               $data['message'] = 'Personal Info Successfully Saved.';
               return response()->json($data,200);
           }

            $request->session()->flash('success','Personal Info Successfully Saved.');

            if($request->has('save_personal_and_next')){
                return redirect('/employee/add/'.$request->userId.'/education');
            }
            return redirect('/employee/add/'.$request->userId.'/personal');

       }catch (\Exception $e){
           if($request->ajax()){
               $data['status'] = 'danger';
               $data['statusType'] = 'NotOk';
               $data['code'] = 500;
               $data['type'] = null;
               $data['title'] = 'Error!';
               $data['message'] = 'Personal Info Not Saved.';
               return response()->json($data,500);
           }

           $request->session()->flash('danger','Personal Info Not Saved.');
           return redirect()->back()->withInput();
       }
    }


    /**
     * @post Add And Edit Education Employee Education
     * @param EmployeeEducationRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addEditEducation(EmployeeEducationRequest $request){

        try{
            $message = '';
            if($request->hasFile('certificate_file')){
                $certificate = time().'.'.$request->certificate_file->extension();
                $request->offsetSet('certificate',$certificate);
                if($request->certificate_file->storeAs(Session('config_id').'/'.$request->userId, $certificate)){
                    if($request->has('old_image')) {
                        File::delete('files/'.Session('config_id').'/'.$request->userId.'/'.$request->old_image);
                    }
                }
            }

            if($request->id) {
                $message = 'Education Successfully Update.';
                $request->offsetSet('updated_by', $this->auth->id);
                if(!EmployeeEducation::find($request->id)->update($request->all())){
                    if(isset($certificate)) {
                        File::delete('files/'.Session('config_id').'/'.$request->userId.'/'.$certificate);
                    }
                }
            }else{
                $message = 'Education Successfully Saved.';
                $request->offsetSet('created_by', $this->auth->id);
                if(!EmployeeEducation::create($request->all())){
                    if(isset($certificate)) {
                        File::delete('files/'.Session('config_id').'/'.$request->userId.'/'.$certificate);
                    }
                }
            }

            if($request->ajax()){
                $education = $this->user->get_user_data_by_user_tab($request->userId,'education');
                $data['data'] = $education->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_education_and_next'))?'experience':null;
                $data['title'] = 'Success!';
                $data['message'] = $message;
                return response()->json($data,200);
            }

            $request->session()->flash('success',$message);
            return redirect()->back();

        }catch(\Exception $e){

            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Education Successfully Saved.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Education Successfully Saved.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @post Add Employee Experience
     * @param EmployeeExperienceRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addEditExperience(EmployeeExperienceRequest $request){
        // dd($request->all());
        try{
            $message = '';
            if($request->id) {
                $message = 'Experience Successfully Update.';
                $request->offsetSet('updated_by', $this->auth->id);
                EmployeeExperience::find($request->id)->update($request->all());
            }else{
                $message = 'Experience Successfully Saved.';
                $request->offsetSet('created_by', $this->auth->id);
                EmployeeExperience::create($request->all());
            }

            if($request->ajax()){
                $experience = $this->user->get_user_data_by_user_tab($request->userId,'experience');
                $data['data'] = $experience->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_experience_and_next'))?'salary':null;
                $data['title'] = 'Success!';
                $data['message'] = $message;
                return response()->json($data,200);
            }

            $request->session()->flash('success',$message);
            return redirect()->back();

        }catch(\Exception $e){

            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Experience Not Saved.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Experience Not Saved.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @post Add Employee Salary
     * @param EmployeeSalaryRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addSalary(EmployeeSalaryRequest $request){

        DB::beginTransaction();
        try{
            $request->offsetSet('created_by', $this->auth->id);

            User::where('id',$request->userId)->update([
                'gross_salary' => $request->gross_salary,
                'basic_salary' => $request->basic_salary,
                'salary_in_cache' => $request->salary_in_cache,
                'effective_date' => $request->effective_date,
            ]);

            if($request->has('salary_info')){

                $salary_info = $request->salary_info;
                $saveData = [];
                // dd($salary_info);
                foreach($salary_info as $sinfo){
                    $saveData[] = [
                        'user_id' => $request->userId,
                        'basic_salary_info_id' => $sinfo['id'],
                        'salary_amount' => ($sinfo['amount'])?$sinfo['amount']:'0',
                        'salary_amount_type' => (isset($sinfo['type']))?$sinfo['type']:'percent',
                        'salary_effective_date' => ($sinfo['date']) ?: date('Y-m-d'),
                        'created_by' => $this->auth->id,
                        'created_at' => date('Y-m-d')
                    ];
                }

                EmployeeSalary::where('user_id',$request->userId)->delete();
                EmployeeSalary::insert($saveData);
            }else{
                if(EmployeeSalary::where('user_id',$request->userId)->count() >= 0){
                    EmployeeSalary::where('user_id',$request->userId)->delete();
                }
            }

            if($request->has('bank_id') && $request->has('bank_account_no')){
                EmployeeSalaryAccount::create($request->all());
            }

            DB::commit();

            if($request->ajax()){
                // $data['data'] = User::with('salaries.basicSalaryInfo','salaryAccount')->find($request->userId);
                $salary = $this->user->get_user_data_by_user_tab($request->userId, 'salary','add');
                $data['data'] = $salary->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_salary_and_next'))?'nominee':null;
                $data['title'] = 'Success!';
                $data['message'] = 'Salary Successfully Saved.';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Salary Successfully Saved.');

            if($request->has('save_salary_and_next')){
                return redirect('/employee/add/'.$request->userId.'/nominee');
            }
            return redirect('/employee/add/'.$request->userId.'/salary');

        }catch(\Exception $e){
            DB::rollback();

            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Salary Not Saved.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Salary Not Saved.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @param EmployeeNomineeRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function addEditNominee(EmployeeNomineeRequest $request){
         try{
             $message = '';
            if($request->hasFile('image')){
                $image = time().'.'.$request->image->extension();
                $request->offsetSet('nominee_photo',$image);
                if($request->image->storeAs(Session('config_id').'/'.$request->userId, $image)){
                    if($request->has('old_image')) {
                        File::delete('files/'.Session('config_id').'/'.$request->userId.'/'.$request->old_image);
                    }
                }
            }

             if($request->id) {
                 $message = 'Nominee Successfully Update.';
                 $request->offsetSet('updated_by', $this->auth->id);
                 if(!EmployeeNominee::find($request->id)->update($request->all())){
                     if(isset($image)) {
                         File::delete('files/'.Session('config_id').'/'.$request->userId.'/'.$image);
                     }
                 }
             }else{
                 $message = 'Nominee Successfully Saved.';
                 $request->offsetSet('created_by', $this->auth->id);
                 if(!EmployeeNominee::create($request->all())){
                     if(isset($image)) {
                         File::delete('files/'.Session('config_id').'/'.$request->userId.'/'.$image);
                     }
                 }
             }

            if($request->ajax()){
                $nominee = $this->user->get_user_data_by_user_tab($request->userId,'nominee');
                $data['data'] = $nominee->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_nominee_and_next'))?'training':null;
                $data['title'] = 'Success!';
                $data['message'] = $message;
                return response()->json($data,200);
            }

            $request->session()->flash('success',$message);
            return redirect()->back();

         }catch(\Exception $e){
             if($request->ajax()){
                 $data['status'] = 'danger';
                 $data['statusType'] = 'NotOk';
                 $data['code'] = 500;
                 $data['title'] = 'Error!';
                 $data['message'] = 'Nominee Not Saved.';
                 $data['data'] = '';
                 return response()->json($data,500);
             }

             $request->session()->flash('danger','Nominee Not Saved.');
             return redirect()->back()->withInput();
         }
    }



    /**
     * @post Add Employee Training
     * @param EmployeeTrainingRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addEditTraining(EmployeeTrainingRequest $request){
        try{
            $message = '';
            if($request->id) {
                $message = 'Training Successfully Update.';
                $request->offsetSet('updated_by', $this->auth->id);
                EmployeeTraining::find($request->id)->update($request->all());
            }else{
                $message = 'Training Successfully Saved.';
                $request->offsetSet('created_by', $this->auth->id);
                EmployeeTraining::create($request->all());
            }

            if($request->ajax()){
                $training = $this->user->get_user_data_by_user_tab($request->userId,'training');
                $data['data'] = $training->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_training_and_next'))?'reference':null;
                $data['title'] = 'Success!';
                $data['message'] = $message;
                return response()->json($data,200);
            }

            $request->session()->flash('success',$message);
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Training Not Saved.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Training Not Saved.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @post Add Employee Reference
     * @param EmployeeReferenceRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addEditReference(EmployeeReferenceRequest $request){
        try{

            $message = '';
            if($request->id) {
                $message = 'Reference Successfully Update.';
                $request->offsetSet('updated_by', $this->auth->id);
                EmployeeReference::find($request->id)->update($request->all());
            }else{
                $message = 'Reference Successfully Saved.';
                $request->offsetSet('created_by', $this->auth->id);
                EmployeeReference::create($request->all());
            }

            if($request->ajax()){
                $reference = $this->user->get_user_data_by_user_tab($request->userId,'reference');
                $data['data'] = $reference->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_reference_and_next'))?'children':null;
                $data['title'] = 'Success!';
                $data['message'] = 'Reference Successfully Saved.';
                return response()->json($data,200);
            }

            $request->session()->flash('success',$message);
            return redirect()->back();

        }catch(\Exception $e){

            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Reference Not Saved.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Reference Not Saved.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @post Add Employee Children
     * @param EmployeeChildrenRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addEditChildren(EmployeeChildrenRequest $request){
        try{
            $message = '';
            if($request->id) {
                $message = 'Children Successfully Update.';
                $request->offsetSet('updated_by', $this->auth->id);
                EmployeeChildren::find($request->id)->update($request->all());
            }else{
                $message = 'Children Successfully Saved.';
                $request->offsetSet('created_by', $this->auth->id);
                EmployeeChildren::create($request->all());
            }

            if($request->ajax()){
                $children = $this->user->get_user_data_by_user_tab($request->userId,'children');
                $data['data'] = $children->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_children_and_next'))?'language':null;
                $data['title'] = 'Success!';
                $data['message'] = $message;
                return response()->json($data,200);
            }

            $request->session()->flash('success',$message);
            return redirect()->back();

        }catch(\Exception $e){

            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Children Not Saved.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Children Not Saved.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @post Add Employee Language
     * @param EmployeeLanguageRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addEditLanguage(EmployeeLanguageRequest $request){
        try{

            $message = '';
            if($request->id) {
                $message = 'Language Successfully Update.';
                $request->offsetSet('updated_by', $this->auth->id);
                EmployeeLanguage::find($request->id)->update($request->all());
            }else{
                $message = 'Language Successfully Saved.';
                $request->offsetSet('created_by', $this->auth->id);
                EmployeeLanguage::create($request->all());
            }

            if($request->ajax()){
                $language = $this->user->get_user_data_by_user_tab($request->userId,'language');
                $data['data'] = $language->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = null;
                $data['title'] = 'Success!';
                $data['message'] = $message;
                return response()->json($data,200);
            }

            $request->session()->flash('success',$message);
            return redirect()->back();

        }catch(\Exception $e){

            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Language Not Saved.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Language Not Saved.');
            return redirect()->back()->withInput();
        }
    }



/********************** Edit Employee Information Functions ********************************/

    public function showEmployeeEditForm(Request $request){
        $data['sidebar_hide'] = 1;
        $data['tab'] = $request->tab;

        if($user = User::find($request->id)){
            $data['user'] = $user;
            $data['id'] = $user->id;

            if($request->ajax()){
                $tabData =  $this->user->get_user_data_by_user_tab($user->id, $request->tab);
                return $tabData;
            }
        }else{
            return redirect()->back();
        }

        return view('pim.employee.edit')->with($data);
    }


    public function getDataByTabAndId(Request $request){

        if($request->data_tab == 'education'){
            $data = EmployeeEducation::with('institute.educationLevel','degree')->find($request->data_id);
        }
        if($request->data_tab == 'experience'){
            $data = EmployeeExperience::find($request->data_id);
        }
        if($request->data_tab == 'nominee'){
            $data = EmployeeNominee::find($request->data_id);
        }
        if($request->data_tab == 'training'){
            $data = EmployeeTraining::find($request->data_id);
        }
        if($request->data_tab == 'reference'){
            $data = EmployeeReference::find($request->data_id);
        }
        if($request->data_tab == 'children'){
            $data = EmployeeChildren::find($request->data_id);
        }
        if($request->data_tab == 'language'){
            $data = EmployeeLanguage::find($request->data_id);
        }

        return response()->json($data);
    }



    /**
     * Update Employee Account with Basic Information
     * @param EmployeeBasicInfoRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editEmployee(EmployeeBasicInfoRequest $request){

        try{
            
            if($request->old_email != $request->email){

                Artisan::call('db:connect');
                UserEmails::where('email',$request->old_email)->where('config_id',Session('config_id'))->update(['email' => $request->email]);

                dispatch(new UserEmailUpdate($request->all()));
            }

            $request->offsetUnset('old_email');

            Artisan::call("db:connect", ['database' => Session('database')]);

            DB::beginTransaction();

            if(!$request->has('password')){
                $request->offsetUnset('password');
            }else{
                $request->offsetSet('password', bcrypt($request->password));
            }

            if($request->hasFile('image')){
                $photo = time().'.'.$request->image->extension();
                if($request->image->storeAs(Session('config_id').'/'.$request->userId,$photo)){
                    if($request->has('old_image')) {
                        File::delete('files/'.Session('config_id').'/'.$request->userId.'/'.$request->old_image);
                    }
                }else{
                    $request->session()->flash('warning','Photo Not Upload.Update photo form edit.');
                }
                $request->offsetSet('photo',$photo);
            }

            $request->offsetSet('updated_by',$this->auth->id);
            $user = User::find($request->userId);
            $oldEmpTypeId = $user->employee_type_id;
            $user->update($request->all());

            $address = EmployeeAddress::findUser($request->userId);

            if($address){
                $address->update($request->all());
            }else{
               EmployeeAddress::create($request->all());
            }

            //  Insert Data Into Leave & Permission
            $this->insertLeavePermissionEdit($user, $request->designation_id, $request->employee_type_id, $oldEmpTypeId);


            // ####@@***@@#### User Employee Type Map Start*********
            
            $currentDataForUpdate = UserEmployeeTypeMap::find($request->current_employee_map_type_id);

            $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
            $current_date = $date->format('Y-m-d');

            if(strtotime($currentDataForUpdate->from_date) > strtotime($current_date)){

                //date validation
                if(strtotime($request->from_date) <= strtotime($current_date)){

                    DB::rollback();
                    if($request->ajax()){
                        $data['status'] = 'danger';
                        $data['statusType'] = 'NotOk';
                        $data['code'] = 500;
                        $data['type'] = null;
                        $data['title'] = 'Error!';
                        $data['message'] = 'Please insert valid effective date!';
                        return response()->json($data,500);
                    }
                }
            }
            else{
                //current emp emptype from date can not editable becaus from date 
                //must be in past... so only probation and contractual emp to_date is 
                //editable.

                //date validation
                if(strtotime($request->from_date) != strtotime($currentDataForUpdate->from_date)) {
                    
                    DB::rollback();

                    if($request->ajax()){
                        $data['status'] = 'danger';
                        $data['statusType'] = 'NotOk';
                        $data['code'] = 500;
                        $data['type'] = null;
                        $data['title'] = 'Error!';
                        $data['message'] = 'Please insert valid effective date....!';
                        return response()->json($data,500);
                    }
                }
                else{
                    if(strtotime($request->to_date) != strtotime($currentDataForUpdate->to_date) && strtotime($request->to_date) <= strtotime($current_date) && ($request->employee_type_id == 2 || $request->employee_type_id == 4)){

                        DB::rollback();

                        if($request->ajax()){
                            $data['status'] = 'danger';
                            $data['statusType'] = 'NotOk';
                            $data['code'] = 500;
                            $data['type'] = null;
                            $data['title'] = 'Error!';
                            $data['message'] = 'Please insert valid effective date....!';
                            return response()->json($data,500);
                        }
                    }
                }
            }

            $type_map = UserEmployeeTypeMap::where('user_id',$request->userId)->orderBy('id','desc')->first();

            $request->offsetSet('updated_by',$this->auth->id);
            $currentDataForUpdate->update($request->all());

            //@@@@@@ Depand on type remove status --Start
            if($request->employee_type_id == 2 || $request->employee_type_id == 4){

                $forRemove = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $currentDataForUpdate->id)->where('from_date', '>=', $request->to_date);

                if(count($forRemove) > 0){
                    $forRemove->delete();
                }

                $updateToDate = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $currentDataForUpdate->id)->orderBy('id', 'DESC')->where('to_date', '>=', $request->to_date)->first();
                
                if(count($updateToDate) > 0){
                    $updateToDate->to_date = null;
                    $updateToDate->save();
                }

                //change userTBL status and with_mapTBL status it increase emp to date

                $chk = EmpTypeMapWithEmpStatus::where('user_emp_type_map_id', $currentDataForUpdate->id)->orderBy('id', 'DESC')->first();

                if($chk->employee_status_id != 1 && strtotime($chk->from_date) < strtotime($current_date)){

                    $sav = new EmpTypeMapWithEmpStatus;
                    $sav->user_emp_type_map_id = $currentDataForUpdate->id;
                    $sav->employee_status_id = 1;
                    $sav->from_date = $current_date;
                    $sav->remarks = "Generated When Emp Edit";
                    $sav->created_by = Auth::user()->id;
                    $sav->save();

                    $savUser = User::find($currentDataForUpdate->user_id)->update(['status' => 1]);

                    //update EmpTypeMapWithEmpStatus
                    $chk->to_date = date('Y-m-d', strtotime($current_date .' -1 day'));
                    $chk->save();
                }
            }
            //@@@@@@ Depand on type remove status --Finished


            //only one future type can be added for a emp
            if($type_map->employee_type_id != $currentDataForUpdate->employee_type_id){

                //this condition only full fill when current date ex: 2017-05-15
                //and from date and to date are ex: 2017-05-10 n 2017-05-25
                //with out to_date value can't insert here coz only from_date <= current date
                //not editable and from_date > current date is future date.. one emp has only one 
                //future type .. 
                if(strtotime($request->to_date) >= strtotime($type_map->to_date) && ( $type_map->employee_type_id == 2 || $type_map->employee_type_id == 4)) {

                    $type_map->delete();                    
                }
                else{
                    $type_map->from_date = date('Y-m-d', strtotime($request->to_date .' 1 day'));
                    $type_map->save();
                }
            }

            // ####@@***@@#### User Employee Type Map Finished*********

            //Just updating EarnLeaveJOb
            dispatch(new CalculateEarnLeaveJob());

            DB::commit();

            if($request->ajax()){
                $userData = $this->user->get_user_data_by_user_tab($request->userId, $request->tab);
                $data['data'] = $userData->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('update_employee_and_next'))?'personal':null;
                $data['title'] = 'Success!';
                $data['message'] = 'Employee Successfully Update!!!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Employee Successfully Update!');

            if($request->has('update_next')){
                return redirect('/employee/edit/'.$request->userId.'/personal');
            }

            return redirect('/employee/edit/'.$request->userId);

        }catch(\Exception $e){

            DB::rollback();
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Employee Not Update!';
                return response()->json($data,500);
            }
            $request->session()->flash('danger','Employee Not Update!');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @post Edit Employee Personal Info
     * @param EmployeePersonalInfoRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editPersonalInfo(EmployeePersonalInfoRequest $request){
        try {
            $request->offsetSet('updated_by',$this->auth->id);
            if($employeeDetails = EmployeeDetail::findUser($request->userId)){
                $employeeDetails->update($request->all());
            }else{
                $request->offsetSet('created_by',$this->auth->id);
                EmployeeDetail::create($request->all());
            }

            $data['data'] = User::with('details.bloodGroup')->find($request->userId);

            if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_personal_and_next'))?'education':null;
                $data['title'] = 'Success!';
                $data['message'] = 'Personal Info Successfully Update.';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Personal Info Successfully Update.');

            if($request->has('save_personal_and_next')){
                return redirect('/employee/edit/'.$request->userId.'/education');
            }
            return redirect('/employee/edit/'.$request->userId.'/personal');

        }catch (\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Personal Info Not Update.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Personal Info Not Update.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @post Edit Employee Salary
     * @param EmployeeSalaryRequest $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editSalary(EmployeeSalaryRequest $request){
        // dd($request->all());
        DB::beginTransaction();
        try{
            $request->offsetSet('updated_by', $this->auth->id);
            $request->offsetSet('user_id', $request->userId);

            User::where('id',$request->userId)->update([
                'gross_salary' => $request->gross_salary,
                'basic_salary' => $request->basic_salary,
                'salary_in_cache' => $request->salary_in_cache,
                'effective_date' => $request->effective_date,
            ]);

            
            if($request->has('salary_info')){

                $salary_info = $request->salary_info;
                $saveData = [];
                // dd($salary_info);
                foreach($salary_info as $sinfo){
                    $saveData[] = [
                        'user_id' => $request->userId,
                        'basic_salary_info_id' => $sinfo['id'],
                        'salary_amount' => ($sinfo['amount'])?$sinfo['amount']:'0',
                        'salary_amount_type' => (isset($sinfo['type']))?$sinfo['type']:'percent',
                        'salary_effective_date' => ($sinfo['date']) ?: date('Y-m-d'),
                        'created_by' => $this->auth->id,
                        'created_at' => date('Y-m-d')
                    ];
                }

                EmployeeSalary::where('user_id',$request->userId)->delete();
                EmployeeSalary::insert($saveData);
            }else{
                if(EmployeeSalary::where('user_id',$request->userId)->count() >= 0){
                    EmployeeSalary::where('user_id',$request->userId)->delete();
                }
            }

            if($request->has('bank_id') && $request->has('bank_account_no')){
                if($request->salary_account_id && !empty($request->salary_account_id)) {
                    EmployeeSalaryAccount::find($request->salary_account_id)->update($request->all());
                }else{
                    EmployeeSalaryAccount::create($request->all());
                }
            }

            DB::commit();

            if($request->ajax()){
                // $data['data'] = User::with('salaries.basicSalaryInfo','salaryAccount')->find($request->userId);
                $salary = $this->user->get_user_data_by_user_tab($request->userId, 'salary','edit');
                $data['data'] = $salary->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('save_salary_and_next'))?'nominee':null;
                $data['title'] = 'Success!';
                $data['message'] = 'Salary Successfully Update.';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Salary Successfully Update.');

            if($request->has('save_salary_and_next')){
                return redirect('/employee/edit/'.$request->userId.'/nominee');
            }
            return redirect('/employee/edit/'.$request->userId.'/salary');

        }catch(\Exception $e){
            DB::rollback();

            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = 'Salary Not Saved.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Salary Not Update.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * @Delete Employee Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteEmployeeData(Request $request){
        if($request->ajax()){
            try{
                if($request->segment(4) == 'education'){
                    EmployeeEducation::where('id',$request->id)->delete();
                }
                if($request->segment(4) == 'experience'){
                    EmployeeExperience::where('id',$request->id)->delete();
                }
                if($request->segment(4) == 'nominee'){
                    EmployeeNominee::where('id',$request->id)->delete();
                }
                if($request->segment(4) == 'training'){
                    EmployeeTraining::where('id',$request->id)->delete();
                }
                if($request->segment(4) == 'reference'){
                    EmployeeReference::where('id',$request->id)->delete();
                }
                if($request->segment(4) == 'children'){
                    EmployeeChildren::where('id',$request->id)->delete();
                }
                if($request->segment(4) == 'language'){
                    EmployeeLanguage::where('id',$request->id)->delete();
                }

                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = ucfirst($request->segment(4)).' Successfully Deleted.';
                return response()->json($data,200);

            }catch(\Exception $e){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['type'] = null;
                $data['title'] = 'Error!';
                $data['message'] = ucfirst($request->segment(4)).' Not Deleted.';
                return response()->json($data,500);
            }
        }
    }


    public function deleteEmployee($employee_id){
        return redirect()->back();
    }


    public function statusChange(Request $request){
        try{
            $status = ($request->status == 'Active')?1:0;
            $user = User::find($request->id);
            $user->status = $status;
            $user->save();

            $data['status'] = 'success';
            $data['statusType'] = 'OK';
            $data['code'] = 200;
            $data['title'] = 'Success!';
            $data['message'] = "<strong class='text-info'>".$user->first_name.' '.$user->last_name.'</strong> Account Successfully '.$request->status;
            return response()->json($data,200);
        }catch(\Exception $e){
            $data['status'] = 'danger';
            $data['statusType'] = 'NotOk';
            $data['code'] = 500;
            $data['type'] = null;
            $data['title'] = 'Error!';
            $data['message'] = "<strong class='text-info'>".$user->first_name.' '.$user->last_name.'</strong> Account Not '.$request->status;
            return response()->json($data,500);
        }
    }

    public function leave($id){

        $currentYear = date('Y');
        $data['individual_user_leaves'] = UserLeaveTypeMap::where('user_id', $id)->where('status', 1)->where('active_from_year', '<=', $currentYear)->where('active_to_year', '>=', $currentYear)->get();
        $data['personalInfo'] = User::find($id);

        return $data;
    }

    public function updateLeave(Request $request){

        $this->validate($request, [
            'hdn_id' => 'required'
        ]);

        $currentYear = date('Y');

        try {
            foreach($request->user_leaves as $key=>$value){
                if($value == 0){
                    $uncheckedAray[] = $key;
                }
                else{
                    $checkedAray[] = $key;    
                }
            }

            if(!empty($uncheckedAray)){
                UserLeaveTypeMap::where('user_id', $request->hdn_id)->where('status', 1)
                                ->where('active_from_year', '<=', $currentYear)
                                ->whereIn('leave_type_id', $uncheckedAray)->delete();
            }

            if(!empty($checkedAray)){

                $exist_leave_id = UserLeaveTypeMap::select('leave_type_id')->where('user_id', $request->hdn_id)->where('status', 1)->where('active_from_year', '<=', $currentYear)->where('active_to_year', '>=', $currentYear)->get()->toArray();

                $exist_leave_id_ary = array_column($exist_leave_id, 'leave_type_id');

                $aryDiff = array_diff($checkedAray,$exist_leave_id_ary);
                
                if(!empty($aryDiff)){
                    $diff_type_value = LeaveType::whereIn('id', $aryDiff)->where('leave_type_status', 1)->where('leave_type_active_from_year', '<=', $currentYear)->where('leave_type_active_to_year', '>=', $currentYear)->get();

                        foreach($diff_type_value as $info){

                            if($info->leave_type_is_earn_leave == 1){
                                $num_leave_days = 0;
                            }
                            else{
                                $num_leave_days = $info->leave_type_number_of_days;
                            }
                            
                            $diff_arry[] = [
                                'user_id' => $request->hdn_id,
                                'leave_type_id' => $info->id,
                                'number_of_days' => $num_leave_days,
                                'active_from_year' => $info->leave_type_active_from_year,
                                'active_to_year' => $info->leave_type_active_to_year,
                                'status' => 1,
                            ];
                        }

                    UserLeaveTypeMap::insert($diff_arry);
                }   
            }

            $request->session()->flash('success','Data successfully updatsed!');

        } catch (\Exception $e) {
        
            $request->session()->flash('danger','Data not updated!');
        }

        return redirect('employee/index');
    }

    public function testJobEmpStatus(){

        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $currentDate = $date->format('Y-m-d');

        $all_user = User::all();

        foreach($all_user as $user){

            echo $user->email;

            $dataa = UserEmployeeTypeMap::where('user_id', $user->id)->orderBy('id', 'DESC')->take(2)->get();

            if(count($dataa) == 1){
                // dd($dataa);
                $current_type = $dataa[0]->employee_type_id;
            }
            else{
                if(empty($dataa[0]->to_date) && (strtotime($dataa[0]->from_date) <= strtotime($currentDate))){

                    // dd($dataa[0]);
                    $current_type = $dataa[0]->employee_type_id;
                }
                elseif((strtotime($dataa[0]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[0]->to_date) >= strtotime($currentDate)) && !empty($dataa[0]->to_date)){

                    // dd($dataa[0]);
                    $current_type = $dataa[0]->employee_type_id;
                }
                elseif((strtotime($dataa[1]->from_date) <= strtotime($currentDate)) && (strtotime($dataa[1]->to_date) >= strtotime($currentDate)) && !empty($dataa[1]->to_date)){

                    // dd($dataa[1]);
                    $current_type = $dataa[1]->employee_type_id;
                    $upCommingType = $dataa[0]->id;
                }
                else{
                    // echo "Invalid";
                    // dd($dataa[0]);
                    // $current_type = $dataa[0]->employee_type_id;
                    $current_type = "Invalid";
                }
            }

            echo "====".$current_type."<br>";
        }
    }
}
