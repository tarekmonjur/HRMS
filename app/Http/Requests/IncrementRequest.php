<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncrementRequest extends FormRequest
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
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        if($this->has('increment_id')){
            return [];
        }
        return [
            'user_id' => 'required',
            'increment_type_id' => 'required',
            'increment_amount_type' => 'required',
            'increment_type_amount' => 'required_if:increment_amount_type,percent|max:6',
            'increment_amount' => 'required_if:increment_amount_type,fixed|max:10',
            'increment_effective_date' => 'required',
        ];
    }



    public function attributes(){
        return [
            'user_id' => 'employee name',
            'increment_type_id' => 'increment type',
            'increment_amount_type' => 'amount type',
        ];
    }


}
