<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProvidentFundRequest extends FormRequest
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
        if($this->request->has('pf_status') || $this->request->has('providentFund_id')){
            return [];
        }

        return [
            'user_id' =>'required',
            'pf_effective_date' =>'required|date',
            'pf_interest_calculate' =>'required',
            'pf_percent_amount' =>'required|percentage',
        ];
    }



    public function attributes(){
        return [
            'user_id' => 'employee name',
            'pf_effective_date' => 'effective date',
            'pf_interest_calculate' => 'interest cal:',
            'pf_percent_amount' => 'provident fund percentage of amount',
        ];
    }



}
