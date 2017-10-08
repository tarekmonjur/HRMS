<?php

namespace App\Http\Controllers\Setup;

use App\Models\Setup\Menu;
use App\Models\Setup\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class MenuController extends Controller
{
    public function __construct(){
        $this->middleware('auth:setup');
    }

    public function index(){

    	$data['title'] = "Setup Menus";
    	return view('setup.menu', $data);
    }

    public function getMenus(){
        
        return Menu::with('module', 'parent')->orderBy('menu_parent_id','module_id')->get();
    }

    public function getModule(){

        return Module::where('module_status', 1)->orderBy('id', 'DESC')->get();
    }

    public function getActiveMenus(){

        return Menu::where('menu_status', 1)->orderBy('id', 'DESC')->get();
    }

    public function create(Request $request){

        $this->validate($request, [
            'menu_name' => 'required',
            'module_id' => 'required',
            // 'menu_section_name' => 'required',
            'menu_url' => 'required',
            'menu_status' => 'required',
            'chk_parent' => 'nullable',
            'menu_parent_id' => 'required_if:chk_parent,1',
            // 'menu_name' => 'required_if:chk_parent,1',
            'menu_section_name' => 'required_unless:chk_parent,1',
        ],
        [
            'module_id.required' => 'The select module field is required.',
            'menu_parent_id.required_if'     => 'The menu parent field is required.',
            // 'menu_name.required_if' => 'Menu name filed is required.',
            'menu_section_name.required_unless' => 'Section name filed is required.',
        ]);

        if($request->chk_parent == 1){

            $menu_parent_id = $request->menu_parent_id;
        }
        else{
            $menu_parent_id = 0;
        }

        try{
            $data['data'] = Menu::create([
                'menu_parent_id' => $menu_parent_id,
                'module_id' => $request->module_id,
                'menu_name' => empty($request->menu_name)?'-':$request->menu_name,
                'menu_url' => $request->menu_url,
                'menu_section_name' => empty($request->menu_section_name)?'-':$request->menu_section_name,
                'menu_icon_class' => $request->menu_icon_class,
                'menu_status' => $request->menu_status,
            ]);
        
            $data['title'] = 'success';
            $data['message'] = 'Data successfully added!';

        }catch (\Exception $e) {
            
            $data['title'] = 'error';
            $data['message'] = 'Data not added!';
        }

        return response()->json($data);
    }

    public function update(Request $request){

        $this->validate($request, [
        	'hdn_id' => 'required',
            'edit_menu_name' => 'required',
            'edit_module_id' => 'required',
            'edit_menu_url' => 'required',
            'edit_menu_section_name' => 'required',
            'edit_menu_status' => 'required',
            'chk_parent' => 'nullable',
            'edit_menu_parent_id' => 'required_if:chk_parent,1',
        ],
        [
            'hdn_id.required' => 'Error: not getting menu id.',
            'edit_module_id.required' => 'The select module field is required.',
            'edit_menu_parent_id.required_if'     => 'The menu parent field is required.',
        ]);

        if($request->chk_parent == 1){

            $edit_menu_parent_id = $request->edit_menu_parent_id;
        }
        else{
            $edit_menu_parent_id = 0;
        }


        // try{

            $data['data'] = Menu::where('id',$request->hdn_id)->update([
                'menu_parent_id' => $edit_menu_parent_id,
                'module_id' => $request->edit_module_id,
                'menu_name' => $request->edit_menu_name,
                'menu_url' => $request->edit_menu_url,
                'menu_section_name' => $request->edit_menu_section_name,
                'menu_icon_class' => $request->edit_menu_icon_class,
                'menu_status' => $request->edit_menu_status,
            ]);
        
            $data['title'] = 'success';
            $data['message'] = 'Data successfully updated!';

        // }catch (\Exception $e) {
            
        //     $data['title'] = 'error';
        //     $data['message'] = 'Data not added!';
        // }

        return response()->json($data);
    }

    public function delete($id, $indexId){
        
        $data['indexId'] = $indexId;

        try{
            $data['data'] = Menu::find($id)->delete();
        
            $data['title'] = 'success';
            $data['message'] = 'Data successfully removed!';

        }catch(\Exception $e){
            
            $data['title'] = 'error';
            $data['message'] = 'Data not removed!';
        }

        return response()->json($data);
    }
}
