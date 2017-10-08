<?php

namespace App\Http\Controllers\Payroll;

use App\Models\ProvidentFund;
use App\Models\PfCalculation;

use App\Http\Requests\ProvidentFundRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProvidentFundController extends Controller
{
	protected $auth;

    public function __construct(Auth $auth){
    	$this->middleware('auth:hrms');

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
    }


    public function index(Request $request)
    {
    	if($request->ajax()){
            if($request->id){
                return ProvidentFund::where('user_id',$request->id)->first();
            }
            if($request->pf_id){
                $data['details'] = PfCalculation::where('provident_fund_id', $request->pf_id)->orderBy('id','desc')->get();
                $data['total_debit'] = $data['details']->sum('pf_debit');
                $data['total_credit'] = $data['details']->sum('pf_credit');
                return $data;
            }
    		return ProvidentFund::with('user','approvedBy','createdBy','updatedBy')->orderBy('id','desc')->get();
    	}
        $data['sidebar_hide'] = true;
    	return view('payroll.provident_fund')->with($data);
    }


    public function create(ProvidentFundRequest $request)
    {
        try{
            $request->offsetSet('created_by', $this->auth->id);
            $providentFund = ProvidentFund::create($request->all());

            if($request->ajax()){
                $data['data'] = ProvidentFund::with('user','approvedBy','createdBy','updatedBy')->find($providentFund->id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Provident Fund Successfully Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Provident Fund Successfully Added!');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Provident Fund Not Added!';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Provident Fund not Added!');
            return redirect()->back()->withInput();
        }
    }


    public function edit(Request $request)
    {
        try{
            $providentFund = ProvidentFund::find($request->id);

            if($request->ajax()){
                $data['data'] = $providentFund;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Provident Fund Successfully Found!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Provident Fund Successfully Found!');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Provident Fund Not Found!';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Provident Fund not Found!');
            return redirect()->back()->withInput();
        }
    }


    public function update(ProvidentFundRequest $request)
    {
        try{
            if($request->has('providentFund_id')){
                $id = $request->providentFund_id;
                $request->offsetSet('approved_by', $this->auth->id);
                ProvidentFund::find($id)->update($request->all());
            }else{
                $id = $request->id;
                $request->offsetSet('updated_by', $this->auth->id);
                ProvidentFund::find($id)->update($request->all());
            }

            if($request->ajax()){
                $data['data'] = ProvidentFund::with('user','approvedBy','createdBy','updatedBy')->find($id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Provident Fund Successfully updated!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Provident Fund Successfully updated!');
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

            $request->session()->flash('danger','Provident Fund not updated!');
            return redirect()->back()->withInput();
        }
    }


    public function delete(Request $request)
    {
        try{
            ProvidentFund::where('id',$request->id)->delete();

            if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Provident Fund Successfully Deleted!';
                return response()->json($data,200);
            }
        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Provident Fund Not Delete.';
                return response()->json($data,500);
            }
        }
    }


}
