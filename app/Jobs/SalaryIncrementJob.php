<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Increment;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SalaryIncrementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $toDate = Carbon::now()->format('Y-m-d');

        $increments = Increment::select('increments.id','user_id', \DB::raw('(SUM(increment_amount) + basic_salary) as total_increments'))
                        ->where('approved_by','!=',0)
                        ->where('increment_status',0)
                        ->where('increment_effective_date',$toDate)
                        ->join('users','users.id','=','increments.user_id')
                        ->groupBy('user_id')
                        ->get();

        $increments_id = [];
        foreach($increments as $info){  
            $increments_id[] = $info->id; 
            $updateData = ['basic_salary' => $info->total_increments];
            User::where('id',$info->user_id)->update($updateData);
        }
        Increment::whereIn('id',$increments_id)->update(['increment_status'=>1]);
    }


}
