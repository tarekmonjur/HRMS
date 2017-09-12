<?php

namespace App\Http\Controllers\Payroll;

use App\Models\User;
use App\Models\Increment;

use App\Http\Requests\IncrementRequest;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class IncrementController extends Controller
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
        // \Artisan::call('salary:increment');
        
    	if($request->ajax()){
    		return Increment::with('user','incrementType','approvedBy','createdBy','updatedBy')->orderBy('id','desc')->get();
    	}
        $data['sidebar_hide'] = true;
    	return view('payroll.increment')->with($data);
    }


    public function create(IncrementRequest $request)
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
            if($request->increment_amount_type == 'percent'){
                foreach($users as $info){
                    $increment_amount = ($info->basic_salary * $request->increment_type_amount)/100;
                    $saveData[] = [
                        'user_id' => $info->id,
                        'increment_type_id' => $request->increment_type_id,
                        'increment_amount' => $increment_amount,
                        'increment_amount_type' => $request->increment_amount_type,
                        'increment_type_amount' => $request->increment_type_amount,
                        'increment_effective_date' => $request->increment_effective_date,
                        'increment_remarks' => $request->increment_remarks,
                        'created_by' => $this->auth->id,
                        'created_at' => Carbon::now()->format('Y-m-d'),
                    ];
                }
            }elseif($request->increment_amount_type == 'fixed'){
                foreach($users as $info){
                    $saveData[] = [
                        'user_id' => $info->id,
                        'increment_type_id' => $request->increment_type_id,
                        'increment_amount' => $request->increment_amount,
                        'increment_amount_type' => $request->increment_amount_type,
                        'increment_effective_date' => $request->increment_effective_date,
                        'increment_remarks' => $request->increment_remarks,
                        'created_by' => $this->auth->id,
                        'created_at' => Carbon::now()->format('Y-m-d'),
                    ];
                }
            }
            // dd($saveData);

            $increment = Increment::insert($saveData);

            if($request->ajax()){
                $data['data'] = $increment;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Increment Successfully Added!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Increment Successfully Added!');
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

            $request->session()->flash('danger','Increment Not Added!');
            return redirect()->back()->withInput();
        }
    }


    public function edit(Request $request)
    {
        try{
            $increment = Increment::with('user')->find($request->id);
            if(!$increment){
                if($request->ajax()){
                    $data['status'] = 'danger';
                    $data['statusType'] = 'NotOk';
                    $data['code'] = 500;
                    $data['title'] = 'Error!';
                    $data['message'] = 'Increment Not found.';
                    return response()->json($data,500);
                }
            }

            if($request->ajax()){
                $data['data'] = $increment;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Increment Successfully Find!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Increment Not found.');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Increment Not Find.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Increment Not found.');
            return redirect()->back()->withInput();
        }
    }


    public function update(IncrementRequest $request)
    {
        try{
            if($request->has('increment_id')){
                $request->offsetSet('approved_by',$this->auth->id);
                $id = $request->increment_id;
                $increment = Increment::find($id);
                if($increment->increment_effective_date > Carbon::now()->format('Y-m-d')){
                    $increment->update(['approved_by' => $this->auth->id]);
                }else{
                    if($request->ajax()){
                        $data['status'] = 'danger';
                        $data['statusType'] = 'NotOk';
                        $data['code'] = 500;
                        $data['title'] = 'Error!';
                        $data['message'] = 'Increment not approved.Pleace update effective date first.';
                        return response()->json($data,500);
                    }
                }
            }else{
                $request->offsetSet('updated_by',$this->auth->id);
                if($request->increment_amount_type == 'percent'){
                    $user = User::find($request->user_id);
                    $increment_amount = ($user->basic_salary * $request->increment_type_amount)/100;
                    $request->offsetSet('increment_amount', $increment_amount);
                }elseif($request->increment_amount_type == 'fixed'){
                    $request->offsetSet('increment_type_amount',Null);
                }
                $id = $request->id;
                Increment::find($id)->update($request->all());
            }

            $increment = Increment::with('user','incrementType','approvedBy','createdBy','updatedBy')->find($id);

            if($request->ajax()){
                $data['data'] = $increment;
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Increment Successfully updated!';
                return response()->json($data,200);
            }

            $request->session()->flash('success','Increment Successfully updated!');
            return redirect()->back();

        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Increment not updated.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Increment not updated!');
            return redirect()->back()->withInput();
        }
    }


    public function delete(Request $request){
        try{
            Increment::where('id',$request->id)->delete();

            if($request->ajax()){
                $data['status'] = 'success';
                $data['statusType'] = 'OK';
                $data['code'] = 200;
                $data['title'] = 'Success!';
                $data['message'] = 'Increment Successfully Deleted!';
                return response()->json($data,200);
            }
        }catch(\Exception $e){
            if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Increment Not Delete.';
                return response()->json($data,500);
            }
        }
    }



}