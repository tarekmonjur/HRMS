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

    // public function edit($id){

    // 	$value = Weekend::find($id);
    //     $data['weekend_name'] = $value->weekend; 
    //     $data['weekend_from_date'] = $value->weekend_from; 
    // 	$data['weekend_status'] = $value->status; 
    // 	$data['hdn_id'] = $id;

    // 	return response()->json($data); 
    // }

    // public function update(Request $request){

    //     $date = new \DateTime(null, new \DateTimeZone('Asia/Dhaka'));
    //     $current_date = $date->format('Y-m-d');

    //     $this->validate($request, [
    //         'hdn_id' => 'required',
    //         //'edit_weekend_name' => 'required',
    //         'edit_weekend_from_date' => 'required|after:yesterday',
    //     ]);

    //     // $srt_name = implode(', ', $request->edit_weekend_name);

    //     try{

    //         if(strtotime($request->old_edit_weekend_from_date) <= strtotime($current_date)){
    //             $data['data'] = Weekend::where('id',$request->hdn_id)->update([
    //                 // 'weekend' => $srt_name,
    //                 // 'weekend_from' => $request->edit_weekend_from_date,
    //                 'weekend_to' => $request->edit_weekend_to_date,
    //                 'updated_by' => Auth::user()->id,
    //                 // 'status' => $request->edit_weekend_status,
    //                 'status' => 1,
    //             ]);
            
    //             $data['title'] = 'success';
    //             $data['message'] = 'data successfully updated! from date not changed.';
    //         }
    //         else{
    //             $data['data'] = Weekend::where('id',$request->hdn_id)->update([
    //                 // 'weekend' => $srt_name,
    //                 'weekend_from' => $request->edit_weekend_from_date,
    //                 'updated_by' => Auth::user()->id,
    //                 // 'status' => $request->edit_weekend_status,
    //                 'status' => 1,
    //             ]);
            
    //             $data['title'] = 'success';
    //             $data['message'] = 'data successfully updated!';
    //         }

    //     }catch (\Exception $e) {
            
    //         $data['title'] = 'error';
    //         $data['message'] = 'data not added!';
    //     }

    //     return response()->json($data);
    // }
}
