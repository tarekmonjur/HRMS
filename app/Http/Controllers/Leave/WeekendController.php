<?php

namespace App\Http\Controllers\Leave;

use Auth;
use DB;
use App\Models\Weekend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeekendController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:hrms');
        // $this->middleware('CheckPermissions', ['except' => ['getAllData']]);

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
    }

    public function index(){

        //only for first time when weekend table is empty
        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $current_date = $date->format('Y-m-d'); 
        $dateAry = explode('-', $current_date);
    
        $chk = Weekend::all();
        if(count($chk) == 0){
            Weekend::create([
                'weekend' => 'Friday',
                'weekend_from' => $dateAry[0]."-01-01",
                'weekend_to' => $dateAry[0]."-12-31",
                'status' => 1,
            ]);
        }
        //end

    	return view('leave.weekend');
    }

    public function getAllData(){

        return Weekend::orderBy('id', 'DESC')->get();
    }

    public function create(Request $request){

        $oldData = Weekend::orderBy('id', 'DESC')->first();

        $this->validate($request, [
            'weekend_name' => 'required',
            'weekend_from_date' => 'required|after:'.$oldData->weekend_from,
        ]);

        $weekend_from_date = $request->weekend_from_date;
        $fromDateAry = explode('-', $weekend_from_date);

        $prev_date = date('Y-m-d', strtotime($weekend_from_date .' -1 day'));

        $srt_name = implode(', ', $request->weekend_name);

        $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
        $current_date = $date->format('Y-m-d');

        DB::beginTransaction();

        try{
            if(strtotime($weekend_from_date) <= strtotime($current_date)){
                $status = 1;
                Weekend::where('status', 1)->update(['status' => 2]);
            }
            else{
                $status = 0;
            }

            $oldData->weekend_to = $prev_date;
            $oldData->save();

            Weekend::create([
                'weekend' => $srt_name,
                'weekend_from' => $weekend_from_date,
                'weekend_to' => $fromDateAry[0]."-12-31",
                'created_by' => Auth::user()->id,
                'status' => $status,
            ]);
        
            DB::commit();  
            $data['title'] = 'success';
            $data['message'] = 'data successfully added!';

        }catch (\Exception $e) {
           
           DB::rollback(); 
           $data['title'] = 'error';
           $data['message'] = 'data not added!';
        }

        return response()->json($data);    	
    }

    public function delete($id){

        try{
            $data['data'] = Weekend::find($id)->delete();
        
            $data['title'] = 'success';
            $data['message'] = 'weekend successfully removed!';

        }catch(\Exception $e){
            
            $data['title'] = 'danger';
            $data['message'] = 'weekend not removed!';
        }

        return response()->json($data);
    }
}
