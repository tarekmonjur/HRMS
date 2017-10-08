<?php

namespace App\Jobs;

use App\Models\Loan;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LoanInstallmentUpdateToSalaryGenerate implements ShouldQueue
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
                $loan = $collect->where('is_loan',true)->where('name','loan')->first();
                if(count($loan)>0)
                {
                    $loans = Loan::where('loan_status',1)
                        ->where('approved_by','!=',0)
                        ->where('loan_duration','>','loan_complete_duration')
                        ->get();

                    $salary_month = $this->salaries['salary_month'];
                    foreach($loans as $loan)
                    {
                        $loan_deduction_month = @unserialize($loan->loan_deduction_month);
                        if(is_array($loan_deduction_month))
                        {
                            if(!in_array($salary_month, $loan_deduction_month))
                            {
                                array_push($loan_deduction_month, $salary_month);
                                Loan::find($loan->id)->update([
                                    'loan_complete_duration' => $loan->loan_complete_duration+1,
                                    'loan_deduction_month' => serialize($loan_deduction_month)
                                ]);
                            }
                        }else{
                            $loan_deduction_month = [$salary_month];
                            Loan::find($loan->id)->update([
                                'loan_complete_duration' => $loan->loan_complete_duration+1,
                                'loan_deduction_month' => serialize($loan_deduction_month)
                            ]);
                        }
                    }    
                }
            }
        }
    }

}
