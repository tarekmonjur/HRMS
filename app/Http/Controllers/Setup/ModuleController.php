<?php

namespace App\Http\Controllers\Setup;

use Auth;
use App\Models\Setup\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ModuleController extends Controller
{
    public function __construct(){
        $this->middleware('auth:setup');
    }

    public function index(){
    	$data['title'] = "Setup Modules";

    	return view('setup.module', $data);
    }

    public function getModule(){

        return Module::orderBy('id')->get();
    }

    public function create(Request $request){

        $this->validate($request, [
            'module_name' => 'required',
            'module_icon_class' => 'required',
            'module_status' => 'required',
        ]);

        try{
            $data['data'] = Module::create([
                'module_name'    => $request->module_name,
                'module_icon_class'    => $request->module_icon_class,
                'module_details' => $request->module_details,
                'module_status'  => $request->module_status,
            ]);
        
            $data['title'] = 'success';
            $data['message'] = 'data successfully added!';

        }catch (\Exception $e) {
            
           	$data['title'] = 'danger';
            $data['message'] = 'data not added!';
        }

        return response()->json($data);
    }

    public function update(Request $request){

        $this->validate($request, [
            'hdn_id' => 'required',
            'edit_module_name' => 'required',
            'edit_module_icon_class' => 'required',
            'edit_module_status' => 'required',
        ]);

        try{
            $data['data'] = Module::where('id',$request->hdn_id)->update([
                'module_name'    => $request->edit_module_name,
                'module_icon_class'    => $request->edit_module_icon_class,
                'module_details' => $request->edit_module_details,
                'module_status'  => $request->edit_module_status,
            ]);
        
            $data['title'] = 'success';
            $data['message'] = 'Module successfully updated!';

        }catch (\Exception $e) {
            $data['title'] = 'danger';
            $data['message'] = 'Module not updated!';
        }

        return response()->json($data);
    }

    public function delete($id, $indexId){
        
        $data['indexId'] = $indexId;

        try{
            $data['data'] = Module::find($id)->delete();
        
            $data['title'] = 'success';
            $data['message'] = 'data successfully removed!';

        }catch(\Exception $e){
            
            $data['title'] = 'error';
            $data['message'] = 'data not removed!';
        }

        return response()->json($data);
    }
}
