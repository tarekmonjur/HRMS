<?php

namespace App\Http\Controllers\Payroll;

use App\Models\Loan;
use App\Models\ProvidentFund;

use App\Http\Requests\LoanRequest;

use App\Jobs\DebitProvidentFundByLoanJob;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoanController extends Controller
{
    protected $auth;

    /**
     * LoanController constructor.
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
    		return Loan::with('user','loanType','approvedBy','createdBy','updatedBy')->orderBy('id','desc')->get();
    	}

		$data['sidebar_hide'] = true;
    	return view('payroll.loan')->with($data);
    	
    }


    public function create(LoanRequest $request){
        try{
            $duration = Loan::cal_loan_duration($request->loan_start_date, $request->loan_end_date);
            $request->offsetSet('loan_duration',$duration);

            $request->offsetSet('created_by', $this->auth->id);
            $loan = Loan::create($request->all());

            if($request->ajax()){
                $data['data'] = Loan::with('user','loanType','approvedBy','createdBy','updatedBy')->find($loan->id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Loan Successfully Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Loan Successfully Added!');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Loan Not Added!';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Loan not Added!');
            return redirect()->back()->withInput();
        }
    }


    public function edit(Request $request)
    {
        try{
            $loan = Loan::find($request->id);

            if($request->ajax()){
                $data['data'] = $loan;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Loan Successfully Found!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Loan Successfully Found!');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Loan Not Found!';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Loan not Found!');
            return redirect()->back()->withInput();
        }
    }


    public function update(LoanRequest $request)
    {
        try{
            if($request->has('loan_id')){
                $id = $request->loan_id;
                $request->offsetSet('approved_by', $this->auth->id);
                $loan = Loan::find($id);
                $loan->update($request->all());
            }else{
                $id = $request->id;
                if(!$request->has('loan_status')){
                    $duration = Loan::cal_loan_duration($request->loan_start_date, $request->loan_end_date);
                    $request->offsetSet('loan_duration',$duration);
                }
                $request->offsetSet('updated_by', $this->auth->id);
                Loan::find($id)->update($request->all());
            }

            if($request->ajax()){
                $data['data'] = Loan::with('user','loanType','approvedBy','createdBy','updatedBy')->find($id);
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Loan Successfully Update!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Loan Successfully Update!');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Loan Not Update!';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Loan not Update!');
            return redirect()->back()->withInput();
        }
    }


    public function delete(Request $request)
    {
        try{
            Loan::where('id',$request->id)->delete();

            if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Loan Successfully Deleted!';
                return response()->json($data,200);
            }
        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Loan Not Delete.';
                return response()->json($data,500);
            }
        }
    }




}
