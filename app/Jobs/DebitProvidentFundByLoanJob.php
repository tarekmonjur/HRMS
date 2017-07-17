<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Models\ProvidentFund;
use App\Models\PfCalculation;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DebitProvidentFundByLoanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $loan;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Loan $loanDate)
    {
        $this->loan = $loanDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->loan->loan_aganist == 'pf'){
            $providentFund = ProvidentFund::where('user_id',$this->loan->user_id)->first();
            PfCalculation::create([
                'provident_fund_id' => $providentFund->id,
                'pf_percent' => 0.00,
                'pf_amount' => 0.00,
                'pf_interest_percent' => 0.00,
                'pf_interest_amount' => 0.00,
                'pf_debit' => $this->loan->loan_amount,
                'pf_credit' => 0.00,
                'pf_date' => date('Y-m-d'),
                'pf_remarks' => 'amount debit for loan',
            ]);
        }
    }




}
