<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Loan;
use App\Models\ProvidentFund;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        if($this->request->has('loan_status') || $this->request->has('loan_id')){
            return [];
        }

        if($this->request->has('id')){
            $start_date = 'required';
            $end_date = 'required';
        }else{
            $start_date = 'required|after_or_equal:today';
            $end_date = 'required|after_or_equal:today';
        }

        return [
            'user_id' => 'required',
            'loan_type_id' => 'required',
            'loan_aganist' => 'required',
            'loan_start_date' => $start_date,
            'loan_end_date' => $end_date,
            'loan_amount' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/|greater_then:'.$this->loan_deduct_amount,
            'loan_deduct_amount' => 'required|regex:/^\d{1,6}(\.\d{1,2})?$/|less_then:'.$this->loan_amount,
        ];
    }


    public function attributes()
    {
        return [
            'user_id' => 'employee name',
            'loan_deduct_amount' => 'deduct amount'
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function($validation){
            $loan_validation = $this->checkGetLoan();
            if($loan_validation){
                foreach($loan_validation as $error){
                    $validation->errors()->add($error['field'], $error['msg']);
                }
            }
        });
    }


    protected function checkGetLoan()
    {
        if(!empty($this->loan_aganist) && !empty($this->user_id))
        {
            if($this->loan_aganist == 'PF' && !empty($this->loan_amount))
            {
                if($check_loan = $this->checkProvidentFund()){
                    $message[] = ['field' => 'loan_amount', 'msg' => $check_loan];
                }
            }
            elseif($this->loan_aganist == 'salary' && !empty($this->loan_deduct_amount))
            {
                if($check_loan = $this->checkSalary()){
                    $message = $check_loan;
                }
            }
        }

        return (isset($message))?$message:false;
    }


    protected function checkProvidentFund()
    {
        $providentFund = ProvidentFund::with('details')->where('user_id',$this->user_id)->first();
        if(count($providentFund)>0){
            $debit = $providentFund->details->sum('pf_debit');
            $credit = $providentFund->details->sum('pf_credit');
            // $amount = ($credit > $debit)? $credit - $debit : $debit - $credit;
            $amount = $credit - $debit;
            return ($amount > $this->loan_amount)?false:'provident fund not enough amount';
        }else{
            return false;
        }
    }


    protected function checkSalary()
    {
        if($user = User::find($this->user_id))
        {
            $toDate = Date('Y-m-d');
            $loan = Loan::where('user_id',$this->user_id)->where('loan_end_date','>',$toDate)->where('loan_aganist','salary')->where('approved_by','!=',0)->get();
            if($loan->count() > 0)
            {
                $deduct_amount = $loan->sum('loan_deduct_amount');
                $total_amount = $user->basic_salary - $deduct_amount;
                if($this->loan_deduct_amount > $total_amount){
                    $message[] = ['field' => 'loan_deduct_amount', 'msg' => 'deduct amount must be less then '.$total_amount];
                    $message[] = ['field' => 'loan_warning', 'msg' => 'The employee <a target="_blank" href="'.url('employee/view/'.$user->employee_no).'" class="alert-link">'. $user->fullname .'</a> basic salary is '.$user->basic_salary.' and his/her already loaned per month deduct amount is '. $deduct_amount .'(monthly).'];
                }
            }else{
                if($this->loan_deduct_amount > $user->basic_salary){
                    $message[] = ['field' => 'loan_deduct_amount', 'msg' => 'deduct amount must be less then basic salary'];
                    $message[] = ['field' => 'loan_warning', 'msg' => 'The employee <a target="_blank" href="'.url('employee/view/'.$user->employee_no).'" class="alert-link">'. $user->fullname .'</a> basic salary is '.$user->basic_salary.' deduct amount must be less then basic salary'];
                }
            }
        }
        return (isset($message))?$message:false;
    }





}
