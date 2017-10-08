<?php

namespace App\Http\Controllers\Attendance;

use App\Models\WorkShift;
use App\Models\CommonWorkShift;

use App\Http\Requests\WorkShiftRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class WorkShiftController extends Controller
{

	protected $auth;

    /**
     * EmployeeController constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->middleware('auth:hrms');

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
    }


    public function index(Request $request){

    	if($request->ajax()){
    		return WorkShift::get();
    	}else{
            
            $data['common_val'] = CommonWorkShift::orderBy('id', 'DESC')->first();
	    	return view('attendance.work_shift', $data);
    	}
    }


    public function create(WorkShiftRequest $request){

    	try{
    		$request->offsetSet('created_by',$this->auth->id);
    		$work_shift = WorkShift::create($request->all());

    		if($request->ajax()){
                $data['data'] = $work_shift;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Work shift Successfully Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Work shift Successfully Added!');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Work shift Not Added.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Work shift Not Added!');
            return redirect()->back()->withInput();
    	}
    }



    public function edit(Request $request){

    	try{
    		$workShift = WorkShift::findOrFail($request->id);

    		if($request->ajax()){
                $data['data'] = $workShift;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Work shift successfully find!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Work shift not found.');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Work shift not found.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Work shift not found.');
            return redirect()->back()->withInput();
    	}
    }



    public function update(WorkShiftRequest $request){

    	try{
    		$request->offsetSet('updated_by',$this->auth->id);

    		$work_shift = WorkShift::find($request->id)->update($request->all());

    		if($request->ajax()){
                $data['data'] = $work_shift;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Work shift Successfully updated!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Work shift Successfully updated!');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Work shift not updated.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Work shift not updated!');
            return redirect()->back()->withInput();
    	}
    }


    public function delete(Request $request){
    	try{
    		WorkShift::where('id',$request->id)->delete();

    		if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Work shift Successfully Deleted!';
                return response()->json($data,200);
            }
    	}catch(\Exception $e){
            $code = $e->getCode();

            if($code == '23000'){
                $message = "Cannot delete a parent row of another table.";
            }else{
                $message = "Work shift Not Delete.";
            }

    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = $message;
                return response()->json($data,500);
            }
    	}
    }


}
