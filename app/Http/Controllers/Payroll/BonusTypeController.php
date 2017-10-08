<?php

namespace App\Http\Controllers\Payroll;

use App\Models\BonusType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class BonusTypeController extends Controller
{
    protected $auth;

    /**
     * BonusTypeController constructor.
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
    		return BonusType::with('createdBy','updatedBy')->orderBy('id','desc')->get();
    	}else{
	    	return view('payroll.bonus_type');
    	}
    }


    public function create(Request $request){

    	$this->validate($request,['bonus_type_name' => 'required']);

    	try{
    		$request->offsetSet('created_by',$this->auth->id);
    		$bonusType = BonusType::create($request->all());

    		if($request->ajax()){
                $data['data'] = BonusType::with('createdBy','updatedBy')->find($bonusType->id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Bonus Type Successfully Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Bonus Type Successfully Added!');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Bonus Type Not Added.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Bonus Type Not Added!');
            return redirect()->back()->withInput();
    	}
    }


    public function edit(Request $request){

    	try{
    		$bonusType = BonusType::find($request->id);
    		if(!$bonusType){
    			if($request->ajax()){
	                $data['status'] = 'danger';
	                $data['statusType'] = 'NotOk';
	                $data['code'] = 500;
	                $data['title'] = 'Error!';
	                $data['message'] = 'Bonus Type Not found.';
	                return response()->json($data,500);
            	}
    		}

    		if($request->ajax()){
                $data['data'] = $bonusType;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Bonus Type Successfully Find!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Bonus Type Not found.');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Bonus Type Not Find.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Bonus Type Not found.');
            return redirect()->back()->withInput();
    	}
    }



    public function update(Request $request){
    	if(!$request->has('bonus_type_status')){
	    	$this->validate($request,['bonus_type_name' => 'required']);
    	}

    	try{
            $request->offsetSet('updated_by', $this->auth->id);
    		BonusType::find($request->id)->update($request->all());

    		if($request->ajax()){
                $data['data'] = BonusType::with('createdBy','updatedBy')->find($request->id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Bonus Type Successfully updated!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Bonus Type Successfully updated!');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Bonus Type not updated.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Bonus Type not updated!');
            return redirect()->back()->withInput();
    	}
    }



    public function delete(Request $request){
    	try{
    		BonusType::where('id',$request->id)->delete();

    		if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Bonus Type Successfully Deleted!';
                return response()->json($data,200);
            }
    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                if($e->getCode() == '23000'){
                    $data['message'] = 'Can not delete Bonus Type. Its parent another table data.';
                }else{
                    $data['message'] = 'Bonus Type Not Delete.';
                }
                return response()->json($data,500);
            }
    	}
    }




}

