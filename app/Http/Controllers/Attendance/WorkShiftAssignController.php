<?php

namespace App\Http\Controllers\Attendance;

use App\Models\User;
use App\Models\WorkShiftEmployeeMap;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class WorkShiftAssignController extends Controller
{
    protected $auth;

    /**
     * WorkShiftAssignController constructor.
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
    		return $this->getEmployeeShift($request->segment(3));
    	}
    	return view('attendance.shift_assign');
    }


    protected function getEmployeeShift($id=null){
    	// $workShift = User::select('users.*',\DB::raw('CONCAT(first_name," ",last_name) as full_name'))

    	$workShift = User::with(['workShifts.workShift','workShifts'=>function($q)use($id){
	    		if(!empty($id) && $id !=null){
	    			$q->where('work_shift_id',$id);
	    		}
                $q->where('status',1);
    		}])->where('status','!=',2)->get();

    	$empWorkShifts = [];	
    	foreach($workShift as $wsinfo){

    		$workShift = [];
    		foreach($wsinfo->workShifts as $sinfo){
    			$workShift[] = [
    				'id' => $sinfo->id,
    				'work_shift_id' => $sinfo->work_shift_id,
    				'shift_name' => $sinfo->workShift->shift_name,
    				'start_date' => $sinfo->start_date,
    				'end_date' => $sinfo->end_date,
	    			'days' => explode(',',$sinfo->work_days),
	    			'days_word' => $this->days($sinfo->work_days),
	    			'created_at' => $sinfo->created_at->format('d M Y')
    			];
    		}

    		if(!empty($id) && $id !=null){
    			if(count($workShift)>0){
		    		$empWorkShifts[] = [
		    			'user_id' => $wsinfo->id,
		    			'full_name' => $wsinfo->full_name,
		    			'employee_no' => $wsinfo->employee_no,
		    			'work_shift' => $workShift,
		    		];
	    		}
    		}else{
    			$empWorkShifts[] = [
	    			'user_id' => $wsinfo->id,
	    			'full_name' => $wsinfo->full_name,
	    			'employee_no' => $wsinfo->employee_no,
	    			'work_shift' => $workShift,
	    		];
    		}
    	}

    	// return $empWorkShifts;
    	return response()->json($empWorkShifts);
    	// dd($empWorkShifts);
    }


    protected function days($days){
    	$days = explode(',', $days);
    	$day_word = "";

    	if(is_array($days)){
    		foreach($days as $day){
    			if($day == 1){
    				$day_word .= "Sat, ";
    			}elseif($day == 2){
    				$day_word .= "Sun, ";
    			}elseif($day == 3){
    				$day_word .= "Mon, ";
    			}elseif($day == 4){
    				$day_word .= "Tue, ";
    			}elseif($day == 5){
    				$day_word .= "Wed, ";
    			}elseif($day == 6){
    				$day_word .= "Thu, ";
    			}elseif($day == 7){
    				$day_word .= "Fir.";
    			}
    		}
    	}
    	return $day_word;
    }



    public function assignWorkShift(Request $request){

    	try{
	    	$work_shift = $request->work_shift;
	    	$saveData = [];

            if($request->has('histories')){
                WorkShiftEmployeeMap::whereIn('id',$request->histories)->update(['status' => 0]);
            }

            if($request->has('deleted')){
                WorkShiftEmployeeMap::whereIn('id',$request->deleted)->delete();
            }

	    	if(is_array($work_shift)){
		    	foreach($work_shift as $wsinfo){

		    		$work_shift_map = WorkShiftEmployeeMap::find($wsinfo['id']);

		    		if($work_shift_map){
						$work_shift_map->user_id = $request->user_id;
						$work_shift_map->work_shift_id = (isset($wsinfo['work_shift_id']))?$wsinfo['work_shift_id']:'';
						$work_shift_map->work_days = (isset($wsinfo['work_days']))?implode(',', $wsinfo['work_days']):'';
						$work_shift_map->start_date = (isset($wsinfo['start_date']))?$wsinfo['start_date']:null;
						$work_shift_map->end_date = (isset($wsinfo['end_date']))?$wsinfo['end_date']:null;
						$work_shift_map->updated_by = $this->auth->id;
						$work_shift_map->save();
		    		}else{
		    			if(isset($wsinfo['work_shift_id'])){
				    		$saveData[] = [
				    			'user_id' => $request->user_id,
				    			'work_shift_id' => (isset($wsinfo['work_shift_id']))?$wsinfo['work_shift_id']:'',
				    			'work_days' => (isset($wsinfo['work_days']))?implode(',', $wsinfo['work_days']):'',
				    			'start_date' => (isset($wsinfo['start_date']))?$wsinfo['start_date']:null,
				    			'end_date' => (isset($wsinfo['end_date']))?$wsinfo['end_date']:null,
				    			'created_by' => $this->auth->id,
				    			'created_at' => date('Y-m-d h:i:s'),
				    		];
				    	}
		    		}
		    	}

		    	if(count($saveData)>0){
		    		WorkShiftEmployeeMap::insert($saveData);
		    	}
	    	}

        if($request->ajax()){
            $data['status'] = 'success';
            $data['statusType'] = 'OK';
            $data['code'] = 200;
            $data['title'] = 'Success!';
            $data['message'] = 'Work shift successfully assign!';
            return response()->json($data,200);
        }
            
        $request->session()->flash('success','Work shift successfully assign!');
        return redirect()->back();

    	}catch(\Exception $e){
    		if($request->ajax()){
                $data['status'] = 'danger';
                $data['statusType'] = 'NotOk';
                $data['code'] = 500;
                $data['title'] = 'Error!';
                $data['message'] = 'Work shift not assign.';
                return response()->json($data,500);
            }

            $request->session()->flash('danger','Work shift not assign!');
            return redirect()->back()->withInput();
    	}

    }

    // public function delete(Request $request){

    //     try{
    //         WorkShiftEmployeeMap::find($request->id)->update(['status' => 0]);
    //         $data['title'] = 'success';
    //         $data['message'] = 'data successfully removed!';

    //         if($request->ajax()){
    //             $data['status'] = 'success';
    //             $data['statusType'] = 'OK';
    //             $data['code'] = 200;
    //             $data['title'] = 'Success!';
    //             $data['message'] = 'Work shift successfully moved to history!';
    //             return response()->json($data,200);
    //         }

    //     }catch(\Exception $e){
    //         if($request->ajax()){
    //             $data['status'] = 'danger';
    //             $data['statusType'] = 'NotOk';
    //             $data['code'] = 500;
    //             $data['title'] = 'Error!';
    //             $data['message'] = 'Work shift not move to history.';
    //             return response()->json($data,500);
    //         }
    //     }
    // }




}
