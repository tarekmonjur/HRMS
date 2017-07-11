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

use App\Services\CommonService;
use App\Services\PermissionService;
use App\Jobs\UserEmailUpdate;

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
        $this->middleware('CheckPermissions', ['except' => ['viewEmployeeProfile', 'statusChange', 'permission', 'updatePermission', 'leave', 'updateLeave']]);

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
        $data['users'] = User::with('designation','createdBy','updatedBy')->where('status','!=',2)->orderBy('id','desc')->get();
        $data['modules_permission'] = Module::with('menus','menus.child_menu')->where('module_status', 1)->get();
        $data['sidebar_hide'] = true;
        $data['leave_types'] = LeaveType::where('leave_type_status', 1)->get();
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

            //Insert Data Into Leave & Permission
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
            
            if($request->employee_type_id == 1 || $request->employee_type_id == 2 || $request->employee_type_id == 4){

                UserEmployeeTypeMap::create($request->all());
            }

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
            if($request->employee_type_id == 1 || $request->employee_type_id == 3){
                $request->offsetSet('confirm_date', $request->joining_date);
            }
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

            //Insert Data Into Leave & Permission
            $this->insertLeavePermissionEdit($user, $request->designation_id, $request->employee_type_id, $oldEmpTypeId);

            if($request->employee_type_id == 2 || $request->employee_type_id == 4){
                if($request->type_status == '1'){
                    $request->offsetSet('created_by',$this->auth->id);
                    UserEmployeeTypeMap::create($request->all());
                }elseif($request->type_status == '0'){

                    $type_map = UserEmployeeTypeMap::where('user_id',$request->userId)->orderBy('id','desc')->first();
                    
                    if($type_map){
                        $type_map->update($request->all());
                    }else{
                        $request->offsetSet('created_by',$this->auth->id);
                        UserEmployeeTypeMap::create($request->all());
                    }
                }
            }
            elseif($request->employee_type_id == 1){
                
                $request->offsetSet('confirm_date',date('Y-m-d'));

                if($employeeDetails = EmployeeDetail::findUser($request->userId)){
                    $employeeDetails->update($request->all());
                }

                $type_map = UserEmployeeTypeMap::where('user_id',$request->userId)->orderBy('id','desc')->first();
                if($type_map){
                    $type_map->update($request->all());
                }else{
                    $request->offsetSet('created_by',$this->auth->id);
                    UserEmployeeTypeMap::create($request->all());
                }
            }

            DB::commit();

            if($request->ajax()){
                $userData = $this->user->get_user_data_by_user_tab($request->userId, $request->tab);
                $data['data'] = $userData->original;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['type'] = ($request->has('update_employee_and_next'))?'personal':null;
                $data['title'] = 'Success!';
                $data['message'] = 'Employee Successfully Update!';
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
}
