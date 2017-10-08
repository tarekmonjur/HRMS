<?php

namespace App\Http\Controllers\Payroll;

use App\Models\LoanType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoanTypeController extends Controller
{
	protected $auth;

    /**
     * LoanTypeController constructor.
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
    		return LoanType::with('createdBy','updatedBy')->orderBy('id','desc')->get();
    	}else{
	    	return view('payroll.loan_type');
    	}
    }


    public function create(Request $request){

    	$this->validate($request,['loan_type_name' => 'required']);

    	try{
    		$request->offsetSet('created_by',$this->auth->id);
    		$loanType = LoanType::create($request->all());

    		if($request->ajax()){
    			$data['data'] = LoanType::with('createdBy','updatedBy')->find($loanType->id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Loan Type Successfully Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Loan Type Successfully Added!');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Loan Type Not Added.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Loan Type Not Added!');
            return redirect()->back()->withInput();
    	}
    }


    public function edit(Request $request){

    	try{
    		$loanType = LoanType::find($request->id);
    		if(!$loanType){
    			if($request->ajax()){
	                $data['status'] = 'danger';
	                $data['statusType'] = 'NotOk';
	                $data['code'] = 500;
	                $data['title'] = 'Error!';
	                $data['message'] = 'Loan Type Not found.';
	                return response()->json($data,500);
            	}
    		}

    		if($request->ajax()){
                $data['data'] = $loanType;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Loan Type Successfully Find!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Loan Type Not found.');
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

            $request->session()->flash('danger','Loan Type Not found.');
            return redirect()->back()->withInput();
    	}
    }



    public function update(Request $request){
    	if(!$request->has('loan_type_status')){
	    	$this->validate($request,['loan_type_name' => 'required']);
    	}

    	try{
            $request->offsetSet('updated_by', $this->auth->id);
    		LoanType::find($request->id)->update($request->all());

    		if($request->ajax()){
                $data['data'] = LoanType::with('createdBy','updatedBy')->find($request->id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Loan Type Successfully updated!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Loan Type Successfully updated!');
            return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Loan Type not updated.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Loan Type not updated!');
            return redirect()->back()->withInput();
    	}
    }



    public function delete(Request $request){
    	try{
    		LoanType::where('id',$request->id)->delete();

    		if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Loan Type Successfully Deleted!';
                return response()->json($data,200);
            }
    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                if($e->getCode() == '23000'){
                    $data['message'] = 'Can not delete Loan Type. Its parent another table data.';
                }else{
	                $data['message'] = 'Loan Type Not Delete.';
	            }
                return response()->json($data,500);
            }
    	}
    }



}
