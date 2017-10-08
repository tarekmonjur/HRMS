<?php

namespace App\Http\Controllers\Payroll;

use App\Models\IncrementType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class IncrementTypeController extends Controller
{

    protected $auth;

    /**
     * IncrementTypeController constructor.
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
    		return IncrementType::with('createdBy','updatedBy')->orderBy('id','desc')->get();
    	}else{
	    	return view('payroll.increment_type');
    	}
    }


    public function create(Request $request){

    	$this->validate($request,['increment_type_name' => 'required']);

    	try{
    		$request->offsetSet('created_by',$this->auth->id);
    		$increment = IncrementType::create($request->all());

    		if($request->ajax()){
                $data['data'] = IncrementType::with('createdBy','updatedBy')->find($increment->id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Increment Type Successfully Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Increment Type Successfully Added!');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Increment Type Not Added.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Increment Type Not Added!');
            return redirect()->back()->withInput();
    	}
    }


    public function edit(Request $request){

    	try{
    		$bank = IncrementType::find($request->id);
    		if(!$bank){
    			if($request->ajax()){
	                $data['status'] = 'danger';
	                $data['statusType'] = 'NotOk';
	                $data['code'] = 500;
	                $data['title'] = 'Error!';
	                $data['message'] = 'Increment Type Not found.';
	                return response()->json($data,500);
            	}
    		}

    		if($request->ajax()){
                $data['data'] = $bank;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Increment Type Successfully Find!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Increment Type Not found.');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Increment Type Not Find.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Increment Type Not found.');
            return redirect()->back()->withInput();
    	}
    }



    public function update(Request $request){
    	if(!$request->has('increment_type_status')){
	    	$this->validate($request,['increment_type_name' => 'required']);
    	}

    	try{
            $request->offsetSet('updated_by', $this->auth->id);
    		IncrementType::find($request->id)->update($request->all());

    		if($request->ajax()){
                $data['data'] = IncrementType::with('createdBy','updatedBy')->find($request->id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Increment Type Successfully updated!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Increment Type Successfully updated!');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Increment Type not updated.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Increment Type not updated!');
            return redirect()->back()->withInput();
    	}
    }



    public function delete(Request $request){
    	try{
    		IncrementType::where('id',$request->id)->delete();

    		if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Increment Type Successfully Deleted!';
                return response()->json($data,200);
            }
    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                if($e->getCode() == '23000'){
                    $data['message'] = 'Can not delete Increment Type. Its parent another table data.';
                }else{
                    $data['message'] = 'Increment Type Not Delete.';
                }
                return response()->json($data,500);
            }
    	}
    }





}
