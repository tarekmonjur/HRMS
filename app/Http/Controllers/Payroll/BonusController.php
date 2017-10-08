<?php

namespace App\Http\Controllers\Payroll;

use App\Models\Bonus;
use App\MOdels\User;

use App\Http\Requests\BonusRequest;

use App\Services\CommonService;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class BonusController extends Controller
{
    use CommonService;

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
    		return Bonus::with('user','bonusType','approvedBy','createdBy','updatedBy')->orderBy('id','desc')->get();
    	}
        $data['sidebar_hide'] = true;
    	return view('payroll.bonus')->with($data);
    }


    public function create(BonusRequest $request)
    {
        try{
            // dd($request->all());
            if($request->designation_id == 0 && $request->user_id[0] == 0){
                $users = User::where('status',1)->get();
            }else{
                if($request->user_id[0] == 0){
                    $users = User::where('designation_id',$request->designation_id)->where('status',1)->get();
                }else{
                    $users = User::where('designation_id',$request->designation_id)->whereIn('id',$request->user_id)->get();
                }
            }

            // dd($users);
            $saveData = [];
            if($request->bonus_amount_type == 'percent'){
                foreach($users as $info){
                    $gross_salary = $this->calculateGrossSalary($info->id, $request->bonus_effective_date);
                    // dd($gross_salary);
                    $bonus_amount = ($gross_salary * $request->bonus_type_amount)/100;
                    $saveData[] = [
                        'user_id' => $info->id,
                        'bonus_type_id' => $request->bonus_type_id,
                        'bonus_amount' => $bonus_amount,
                        'bonus_amount_type' => $request->bonus_amount_type,
                        'bonus_type_amount' => $request->bonus_type_amount,
                        'bonus_effective_date' => $request->bonus_effective_date,
                        'bonus_remarks' => $request->bonus_remarks,
                        'created_by' => $this->auth->id,
                        'created_at' => Carbon::now()->format('Y-m-d'),
                    ];
                }
            }elseif($request->bonus_amount_type == 'fixed'){
                foreach($users as $info){
                    $saveData[] = [
                        'user_id' => $info->id,
                        'bonus_type_id' => $request->bonus_type_id,
                        'bonus_amount' => $request->bonus_amount,
                        'bonus_amount_type' => $request->bonus_amount_type,
                        'bonus_effective_date' => $request->bonus_effective_date,
                        'bonus_remarks' => $request->bonus_remarks,
                        'created_by' => $this->auth->id,
                        'created_at' => Carbon::now()->format('Y-m-d'),
                    ];
                }
            }
            // dd($saveData);

            $bonus = Bonus::insert($saveData);

            if($request->ajax()){
                $data['data'] = $bonus;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Bonus Successfully Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Bonus Successfully Added!');
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

            $request->session()->flash('danger','Bonus Not Added!');
            return redirect()->back()->withInput();
        }
    }


    public function edit(Request $request)
    {
        try{
            $bonus = Bonus::with('user')->find($request->id);
            if(!$bonus){
                if($request->ajax()){
                    $data['status'] = 'danger';
                    $data['statusType'] = 'NotOk';
                    $data['code'] = 500;
                    $data['title'] = 'Error!';
                    $data['message'] = 'Bonus Not found.';
                    return response()->json($data,500);
                }
            }

            if($request->ajax()){
                $data['data'] = $bonus;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Bonus Successfully Find!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Bonus Not found.');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Bonus Not Find.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Bonus Not found.');
            return redirect()->back()->withInput();
        }
    }


    public function update(BonusRequest $request)
    {
        try{
            if($request->has('bonus_id')){
                $request->offsetSet('approved_by',$this->auth->id);
                $id = $request->bonus_id;
                Bonus::find($id)->update($request->all());
            }else{
                $request->offsetSet('updated_by',$this->auth->id);
                if($request->bonus_amount_type == 'percent'){
                    $user = User::find($request->user_id);
                    $gross_salary = $this->calculateGrossSalary($user->id, $request->bonus_effective_date);
                    // dd($gross_salary);
                    $bonus_amount = ($gross_salary * $request->bonus_type_amount)/100;
                    $request->offsetSet('bonus_amount', $bonus_amount);
                }elseif($request->bonus_amount_type == 'fixed'){
                    $request->offsetSet('bonus_type_amount',Null);
                }
                $id = $request->id;
                Bonus::find($id)->update($request->all());
            }

            $bonus = Bonus::with('user','bonusType','approvedBy','createdBy','updatedBy')->find($id);

            if($request->ajax()){
                $data['data'] = $bonus;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Bonus Successfully updated!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Bonus Successfully updated!');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Bonus not updated.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Bonus not updated!');
            return redirect()->back()->withInput();
        }
    }


    public function delete(Request $request){
        try{
            Bonus::where('id',$request->id)->delete();

            if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Bonus Successfully Deleted!';
                return response()->json($data,200);
            }
        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Bonus Not Delete.';
                return response()->json($data,500);
            }
        }
    }



}
