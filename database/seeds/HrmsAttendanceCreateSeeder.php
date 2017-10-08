<?php

use App\Models\Attendance;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HrmsAttendanceCreateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$attendances = [];
    	$today_date = Carbon::now()->subYears(15)->format('Y-m-d');
    	
        for($i=1; $i<= 8000; $i++){
    		$in_time = Carbon::now()->format('H:i');
    		$out_time = Carbon::now()->addHours(9)->format('H:i');
    		$total_work_hour = date('H.i',strtotime($out_time) - strtotime($in_time));

    		$date = Carbon::parse($today_date)->format('Y-m-d');
    		$today_date = Carbon::parse($date)->addDay(1);

        	$attendances[] = [
        		'user_id' => 1,
        		'date' => $date,
        		'in_time' => $in_time,
        		'out_time' => $out_time,
        		'total_work_hour' => $total_work_hour,
        		'late_count_time' => Null,
        		'late_hour' => Null,
        		// 'created_at' => $date,
        	];
        	$attendances[] = [
        		'user_id' => 2,
        		'date' => $date,
        		'in_time' => $in_time,
        		'out_time' => $out_time,
        		'total_work_hour' => $total_work_hour,
        		'late_count_time' => Null,
        		'late_hour' => Null,
        		// 'created_at' => $date,
        	];
        	$attendances[] = [
        		'user_id' => 3,
        		'date' => $date,
        		'in_time' => $in_time,
        		'out_time' => $out_time,
        		'total_work_hour' => $total_work_hour,
        		'late_count_time' => Null,
        		'late_hour' => Null,
        		// 'created_at' => $date,
        	];
        }

        echo date('h:i:s');
        foreach(array_chunk($attendances, 1000) as $attendance){
        	Attendance::insert($attendance);
    	}
        echo "---";
        echo date('h:i:s');
    }



}
