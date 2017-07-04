<?php

namespace App\Http\Controllers\Pim;

use App\Http\Requests\LevelRequest;
use App\Models\Level;
use App\Models\BasicSalaryInfo;
use App\Models\LevelSalaryInfoMap;
use App\Models\Module;
use App\Models\Menu;
use App\Models\LevelPermission;
use App\Models\UserPermission;
use App\Models\Designation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Validator;

class LevelController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth:hrms');
        $this->middleware('CheckPermissions', ['except' => ['permission','updatePermission']]);

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
    }

    public function index(){

    	$data['title'] = "Employee Levels-HRMS";
    	$data['levels'] = Level::with('salaryInfo','parent')->orderBy('id','DESC')->get();
        $data['modules_permission'] = Module::with('menus','menus.child_menu')->where('module_status', 1)->get();
        $data['sidevar_hide'] = 1;

        return view('pim.level.levels', $data);
    }

    public function permission($id){

        $data['levels'] = LevelPermission::where('level_id', $id)->get();

        return $data['levels'];
    }

    public function updatePermission(Request $request){

        $this->validate($request, [
            'hdn_id' => 'required',
            'status' => 'required'
        ]);

        $status = $request->status;

        DB::beginTransaction();

        try {
            foreach($request->level_menus as $key=>$value){
                if($value == 0){
                    $uncheckedAray[] = $key;
                }
                else{
                    $checkedAray[] = $key;    
                }
            }

            if(!empty($uncheckedAray)){
                LevelPermission::where('level_id', $request->hdn_id)
                        ->whereIn('menu_id', $uncheckedAray)->delete();
            }

            if(!empty($checkedAray)){
                $exist_menu_obj = LevelPermission::select('menu_id')->where('level_id', $request->hdn_id)
                        ->whereIn('menu_id', $checkedAray)->get()->toArray();
            }

            $exist_menu_ary = array_column($exist_menu_obj, 'menu_id');
            $aryDiff = array_diff($checkedAray,$exist_menu_ary);

            if(!empty($aryDiff)){
                foreach($aryDiff as $info){
                    $level_permission[] = [
                                'level_id' => $request->hdn_id,
                                'menu_id' => $info
                            ];
                }

                LevelPermission::insert($level_permission);
            }

            //if change existing user info STATUS = 1
            if($status ==1){
                $userIdAry = [];
                $levelUsers = Designation::with('user')->where('level_id', $request->hdn_id)->get();

                foreach($levelUsers as $info){
                    foreach($info->user as $user){
                        $userIdAry[] = $user->id;
                    }
                }

                if(!empty($uncheckedAray)){
                    UserPermission::whereIn('user_id', $userIdAry)
                            ->whereIn('menu_id', $uncheckedAray)->delete();
                }

                if(!empty($checkedAray)){

                    $userWithPermission = UserPermission::whereIn('user_id', $userIdAry)->get();

                    //check if this level have user in user permission table
                    if(count($userWithPermission) > 0){
                        foreach($userIdAry as $idInfo){
                            foreach($userWithPermission as $info){
                                if($info->user_id == $idInfo){
                                    $ary[$idInfo][] = $info->menu_id;
                                }
                            }

                            $diffMenuId = array_diff($aryDiff,$ary[$idInfo]);

                            print_r($diffMenuId);
                            if(!empty($diffMenuId)){
                                foreach($diffMenuId as $diff){
                                    $userMenu[] = [
                                                'user_id' => $idInfo,
                                                'menu_id' => $diff,
                                            ];
                                }

                                //insert data into db
                                UserPermission::insert($userMenu);
                                $userMenu = [];
                            }
                        }  
                    }
                }              
            }

            DB::commit();
            $request->session()->flash('success','Data successfully updatsed!');

        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('danger','Data not updated!');
        }

        return redirect('levels/index');
    }

    public function add(){

    	$data['title'] = "Employee Levels Add-HRMS";
    	$data['info'] = "";
        $data['salary_info'] = BasicSalaryInfo::all();
        $data['parents'] = Level::where('status', 1)->get();
        $data['modules_permission'] = Module::with('menus','menus.child_menu')->where('module_status', 1)->get();

        return view('pim.level.add', $data);
    }

    public function create(LevelRequest $request){
        
        if($request->chk_parent == 1){

            $parent_id = $request->parent_id;
        }
        else{
            $parent_id = 0;
        }

        $name = $request->name;
        $salary_amount = $request->salary_amount;
        $description = !empty($request->details)?$request->details:"No description...";
        $status = $request->status;
        $infoChk = $request->salaryInfoChk;
        $level_menus = $request->level_menus;
        $level_effective_date = $request->level_effective_date;
    	
        DB::beginTransaction();

        try {
    		$save = new Level;
            $save->level_name = $name;
    		$save->parent_id = $parent_id;
            $save->level_salary_amount =  $salary_amount;
    		$save->description = $description;
            $save->level_effective_date = $level_effective_date;
    		$save->status = $status;
    		$save->created_by = Auth::user()->id;
    		$save->save();

            foreach($infoChk as $chk){
                if(array_key_exists('chk', $chk)){
                    $data[] = [
                                'level_id' => $save->id, //last insert level id
                                'amount' => $chk['amount'],
                                'basic_salary_info_id' => $chk['id']
                            ];
                }
            }

            if(!empty($data)){
                LevelSalaryInfoMap::insert($data);
            }

            if(!empty($level_menus)){
                foreach($level_menus as $info){
                    $level_permission[] = [
                                'level_id' => $save->id,
                                'menu_id' => $info
                            ];
                }

                if(!empty($level_permission)){
                    LevelPermission::insert($level_permission);
                }
            }

            DB::commit();
            
            $data['title'] = 'success';
            $data['message'] = 'Data successfully added!';

        } catch (\Exception $e) {
            DB::rollback();
            $data['title'] = 'error';
            $data['message'] = 'Data not added!';
        }

    	return response()->json($data);
    }

    public function edit($id){

    	$data['title'] = "Edit Employee Levels-HRMS";
    	$data['info'] = Level::with('salaryInfo','parent')->find($id);
        $data['salary_info'] = BasicSalaryInfo::all();
        $data['parents'] = Level::where('status',1)->get();

        $data['level_salry_info_id'][] = "";
        $data['level_salry_info_ary'][]['amount'] = "";

        if($data['info']->salaryInfo){
            foreach($data['info']->salaryInfo as $info){
                $data['level_salry_info_id'][] = $info->basic_salary_info_id;
                $data['level_salry_info_ary'][$info->basic_salary_info_id]['amount'] = $info->amount;
            }
        }

        return view('pim.level.add', $data);
    }

    public function update(LevelRequest $request){

        $data = [];

        if($request->chk_parent == 1){

            $parent_id = $request->parent_id;
        }
        else{
            $parent_id = 0;
        }

        $id = $request->id;
        $name = $request->name;
        $salary_amount = $request->salary_amount;
        $description = !empty($request->details)?$request->details:"No description...";
        $level_effective_date = $request->level_effective_date;
        $status = $request->status;
        $infoChk = $request->salaryInfoChk;
        
        DB::beginTransaction();

        try {
            $save = Level::find($id);
            $save->level_name = $name;
            $save->parent_id = $parent_id;
            $save->level_salary_amount =  $salary_amount;
            $save->description = $description;
            $save->level_effective_date = $level_effective_date;
            $save->status = $status;
            $save->updated_by = Auth::user()->id;
            $save->save();

            foreach($infoChk as $chk){
        
                if(array_key_exists('chk', $chk)){
                    $data[] = [
                                'level_id' => $save->id, //last insert level id
                                'amount' => $chk['amount'],
                                'basic_salary_info_id' => $chk['id']
                            ];
                }
            }

            if(!empty($data)){
                LevelSalaryInfoMap::where('level_id', $id)->delete();
                LevelSalaryInfoMap::insert($data);
            }
            else{
                LevelSalaryInfoMap::where('level_id', $id)->delete();
            }

            DB::commit();
            
            $request->session()->flash('success','Data updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            $request->session()->flash('danger','Data not updated!');
        }

    	return response()->json($data);
    }

    public function update_info(Request $request){

        $id = $request->id;
        $percent = $request->salryInfoPercent;
        $infoId = $request->salryInfoId;

        $length = count($infoId);

        DB::beginTransaction();

        try {

            for($i=0; $i<$length; $i++){

                if($percent[$i] > 0){
                    $data[] = [
                                'level_id' => $id,
                                'amount' => $percent[$i],
                                'basic_salary_info_id' => $infoId[$i]
                            ];
                }
            }

            LevelSalaryInfoMap::insert($data);

            DB::commit();
            
            $request->session()->flash('success','New info successfully added!');

        } catch (\Exception $e) {
            DB::rollback();
            
            $request->session()->flash('danger','Data not added!');
        }

        return redirect("levels/edit/$id");
    }

    public function delete(Request $request,$id){

        DB::beginTransaction();

        try {
            $del = Level::find($id);
            $del->salaryInfo()->delete();
            $del->levelPermission()->delete();
            $del->delete();

            DB::commit();
            
            $data['title'] = 'success';
            $data['message'] = 'Level removed!';

        } catch (\Exception $e) {
            DB::rollback();

            $data['title'] = 'error';

            if($e->errorInfo[1] == 1451){
                $data['message'] = 'Cannot delete or update a parent row!';
            }
            else{
                $data['message'] = 'Level not removed!';
            }
        }

        return $data;
    }

}
