<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeSalaryRequest extends FormRequest
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
        if($this->request->get('bank_id')){
            $bank_account_no = 'required|digits_between:13,26';
            $bank_account_name = 'required|alpha_spaces';
            $bank_branch_name = 'required|alpha_spaces';
        }else{
            $bank_account_no = 'nullable';
            $bank_account_name = 'nullable';
            $bank_branch_name = 'nullable';
        }

        return [
            'basic_salary' => 'required|numeric',
            'salary_in_cache' => 'nullable|required|numeric',
            'effective_date' => 'required|date',
            'bank_id' => 'nullable|required',
            'bank_account_no' => $bank_account_no,
            'bank_account_name' => $bank_account_name,
            'bank_branch_name' => $bank_branch_name,
            'bank_branch_address' => 'required_with:bank_id',
        ];
    }


    public function attributes(){
        return [
            'bank_id' => 'bank name',
        ];
    }
}
