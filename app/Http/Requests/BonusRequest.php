<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BonusRequest extends FormRequest
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
        if($this->has('bonus_id')){
            return [];
        }
        return [
            'user_id' => 'required',
            'bonus_type_id' => 'required',
            'bonus_amount_type' => 'required',
            'bonus_type_amount' => 'required_if:bonus_amount_type,percent|max:6',
            'bonus_amount' => 'required_if:bonus_amount_type,fixed|max:10',
            'bonus_effective_date' => 'required',
        ];
    }



    public function attributes(){
        return [
            'user_id' => 'employee name',
            'bonus_type_id' => 'bonus type',
            'bonus_amount_type' => 'amount type',
        ];
    }


}
