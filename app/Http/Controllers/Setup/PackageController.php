<?php

namespace App\Http\Controllers\Setup;

use App\Http\Requests\SetupPackage;
use App\Models\Setup\Menu;
use App\Models\Setup\Module;
use App\Models\Setup\Package;
use App\Models\Setup\ModulePackageMap;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class PackageController extends Controller
{
    public function __construct(){
        $this->middleware('auth:setup');
    }

    public function index(){

    	$data['title'] = "Setup|Packages";
    	$data['packages'] = Package::with('modules.singleModule')->orderBy('id','DESC')->get();

        return view('setup.package.view', $data);
    }

    public function add(){

    	$data['title'] = "Setup|Package Add";
    	$data['modules'] = Module::where('module_status',1)->get();

        return view('setup.package.add', $data);
    }

    public function create(SetupPackage $request){

        $package_name = $request->package_name;
        $package_price = $request->package_price;
        $package_type = $request->package_type;
        $package_duration = $request->package_duration;
        $package_level_limit = $request->package_level_limit;
        $package_user_limit = $request->package_user_limit;
        $modules = $request->modules;
        $status = $request->status;
    	
        // DB::beginTransaction();

        // try {
    		$save = new Package;
            $save->package_name = $package_name;
    		$save->package_price = $package_price;
            $save->package_duration =  $package_duration;
    		$save->package_type = $package_type;
    		$save->package_level_limit = $package_level_limit;
    		$save->package_user_limit = $package_user_limit;
    		$save->package_status = $status;
    		$save->package_created_by = Auth::user()->id;
    		$save->save();

    		if(count($modules) > 0){
	        	foreach($modules as $info){
	        		$data[] = [
                                'package_id' => $save->id, //last insert level id
                                'module_id' => $info,
                            ];
	        	}
	        }

            if(!empty($data)){
                ModulePackageMap::insert($data);
            }

            // DB::commit();
            
            $data['title'] = 'success';
            $data['message'] = 'Data successfully added!';

        // } catch (\Exception $e) {
        //     DB::rollback();
        //     $data['title'] = 'error';
        //     $data['message'] = 'Data not added!';
        // }

    	return response()->json($data);
    }

    public function edit($id){

    	$data['title'] = "Setup|Edit Package";
    	$data['info'] = Package::with('modules.singleModule')->where('id', $id)->first();
    	$data['modules'] = Module::where('module_status',1)->get();
    	$chkData = ModulePackageMap::select('module_id')->where('package_id', $id)->get();

        $dataAry = [];

        if($chkData){
            foreach($chkData as $dd){
                $dataAry[] = $dd->module_id;
            }
        }

		$data['chked_modules'] = $dataAry;

        return view('setup.package.add', $data);
    }

    public function update(SetupPackage $request){

        $id = $request->id;
        $package_name = $request->package_name;
        $package_price = $request->package_price;
        $package_type = $request->package_type;
        $package_duration = $request->package_duration;
        $package_level_limit = $request->package_level_limit;
        $package_user_limit = $request->package_user_limit;
        $modules = $request->modules;
        $status = $request->status;
        
        DB::beginTransaction();

        try {
            $save = Package::find($id);
            $save->package_name = $package_name;
    		$save->package_price = $package_price;
            $save->package_duration =  $package_duration;
    		$save->package_type = $package_type;
    		$save->package_level_limit = $package_level_limit;
    		$save->package_user_limit = $package_user_limit;
    		$save->package_status = $status;
    		$save->save();

            if(count($modules) > 0){
	        	foreach($modules as $info){
	        		$data[] = [
                                'package_id' => $save->id, //last insert level id
                                'module_id' => $info,
                            ];
	        	}
	        }

            if(!empty($data)){
                ModulePackageMap::where('package_id', $id)->delete();
                ModulePackageMap::insert($data);
            }

            DB::commit();
            
            $data['title'] = 'success';
            $data['message'] = 'Data successfully updated';

        } catch (\Exception $e) {
            DB::rollback();

            $data['title'] = 'error';
            $data['message'] = 'Data not updated';
        }

    	return response()->json($data);
    }

    public function delete(Request $request,$id){

        DB::beginTransaction();

        try {
            $del = Package::find($id);
            $del->modules()->delete();
            $del->delete();

            DB::commit();
            
            $request->session()->flash('success','Data removed !');

        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('danger','Data not removed !');
        }

        return redirect('packages/index');
    }
}
