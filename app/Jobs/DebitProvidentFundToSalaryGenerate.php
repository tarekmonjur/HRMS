<?php

namespace App\Jobs;

use App\Models\ProvidentFund;
use App\Models\PfCalculation;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DebitProvidentFundToSalaryGenerate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $salaries;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $salaries)
    {
        $this->salaries = $salaries;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(count($this->salaries)>0)
        {
            $deduction_info = unserialize($this->salaries['deduction_info']);
            if(count($deduction_info)>0)
            {
                $collect = collect($deduction_info);
                $provident_fund = $collect->where('name','Provident Fund')->first();
                // dd($provident_fund);
                if(count($provident_fund)>0){
                    $start_date = Carbon::parse($this->salaries['salary_month'])->format('Y-m-d');
                    $end_date = Carbon::parse($this->salaries['salary_month'])->format('Y-m-t');

                    $ProvidentFund = ProvidentFund::select('pf_calculations.*')
                            ->where('user_id', $this->salaries['user_id'])
                            ->where('pf_status',1)
                            ->where('approved_by','!=',0)
                            ->whereBetween('pf_effective_date',[$start_date,$end_date])
                            ->join('pf_calculations','pf_calculations.provident_fund_id','=','provident_funds.id')
                            ->where('pf_observation',1)
                            ->where('pf_date',$this->salaries['salary_month'])
                            ->first();
                    // dd($ProvidentFund);        

                    if($ProvidentFund && count($ProvidentFund)>0){
                          PfCalculation::where('id',$ProvidentFund->id)->update([
                                'pf_percent' => $provident_fund['percent'],
                                'pf_amount' => $provident_fund['amount'],
                                'pf_credit' => $provident_fund['amount'],
                                'updated_at' => Carbon::now()->format('Y-m-d'),
                            ]);

                    }else{
                        $ProvidentFund = ProvidentFund::where('user_id', $this->salaries['user_id'])
                            ->where('pf_status',1)
                            ->where('approved_by','!=',0)
                            ->whereBetween('pf_effective_date',[$start_date,$end_date])
                            ->first();

                        if($ProvidentFund){    
                            $saveData = [
                                'provident_fund_id' => $ProvidentFund->id,
                                'pf_percent' => $provident_fund['percent'],
                                'pf_amount' => $provident_fund['amount'],
                                'pf_interest_percent' => 0.00,
                                'pf_interest_amount' => 0.00,
                                'pf_debit' => 0.00,
                                'pf_credit' => $provident_fund['amount'],
                                'pf_date' => $this->salaries['salary_month'],
                                'pf_observation' => 1,
                                'pf_remarks' => 'amount credit for monthly salary',
                                'created_at' => Carbon::now()->format('Y-m-d'),
                            ];
                            PfCalculation::insert($saveData);
                        }
                    }
                }
            }
        }
    }


}
