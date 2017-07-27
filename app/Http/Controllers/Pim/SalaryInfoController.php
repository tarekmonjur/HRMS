<?php

namespace App\Http\Controllers\Pim;

use App\Models\BasicSalaryInfo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryInfoController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth:hrms');
        $this->middleware('CheckPermissions', ['except' => ['getAllInfo']]);

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
    }

    public function index(){

    	$data['title'] = "Salary Info";
    	$data['salaryInfo'] = BasicSalaryInfo::all();

    	return view('pim.salaryInfo', $data);
    }

    public function getAllInfo(){

    	return BasicSalaryInfo::orderBy('id','DESC')->get();
    }

    public function create(Request $request){

        $this->validate($request, [
            'info_name' => 'required',
            'info_amount' => 'required',
            'info_status' => 'required',
            'info_type' => 'required'
        ]);

        try{
            $chk = BasicSalaryInfo::where('salary_info_name', $request->info_name)->first();

            if(count($chk) > 0){
                $data['title'] = 'danger';
                $data['message'] = 'Duplicate entry !';
            }
            else{
                $data['data'] = BasicSalaryInfo::create([
                    'salary_info_name' => $request->info_name,
                    'salary_info_amount' => $request->info_amount,
                    'salary_info_amount_status' => $request->info_status,
                    'salary_info_type' => $request->info_type,
                ]);
            
                $data['title'] = 'success';
                $data['message'] = 'salary info successfully added!';
            }
                
            return response()->json($data, 200);

        }catch (\Exception $e) {
            
            $data['title'] = 'danger';
            $data['message'] = 'Salary info not added!';
            return response()->json($data, 500);
        }
    }

    public function edit($id){

        $data = BasicSalaryInfo::find($id);
        return response()->json($data);
    }

    public function update(Request $request){

        $this->validate($request, [
            'hdn_id' => 'required',
            'edit_info_name' => 'required',
            'edit_info_amount' => 'required',
            'edit_info_status' => 'required',
            'edit_info_type' => 'required'
        ]);

        try {
            $data['data'] = BasicSalaryInfo::where('id',$request->hdn_id)->update([
                'salary_info_name' => $request->edit_info_name,
                'salary_info_amount' => $request->edit_info_amount,
                'salary_info_amount_status' => $request->edit_info_status,
                'salary_info_type' => $request->edit_info_type,
            ]);
        
            $data['title'] = 'success';
            $data['message'] = 'salary info successfully updated!';
            return response()->json($data);

        } catch (\Exception $e) {
            
            $data['title'] = 'danger';
            $data['message'] = 'Salary info not updated!';
            return response()->json($data);
        }
    }


    public function delete($id, $indexId){
        
        $data['indexId'] = $indexId;

        try{
            $data['data'] = BasicSalaryInfo::find($id)->delete();
        
            $data['title'] = 'success';
            $data['message'] = 'salary info successfully removed!';
            //return response()->json($data, 200);

        }catch(\Exception $e){
            
            $data['title'] = 'danger';
            $data['message'] = 'Salary info not removed!';
            //return response()->json($data, 500);
        }

        return response()->json($data);
    }
}
