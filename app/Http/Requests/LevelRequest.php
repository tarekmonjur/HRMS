<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LevelRequest extends FormRequest
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
        return [
            'name' => 'required',
            'salary_amount' => 'required',
            'chk_parent' => 'nullable',
            'parent_id' => 'required_if:chk_parent,1',
            'salaryInfoChk.*.chk' => 'nullable',
            'salaryInfoChk.*.amount' => 'required_if:salaryInfoChk.*.chk,1',
        ];
    }

    public function messages()
    {
        return [
            'parent_id.required_if'     => 'The parent field is required if level have parent.',
            'salaryInfoChk.*.amount.required_if'  => 'Selected salary info can not be null !!',
        ];
    }
}
