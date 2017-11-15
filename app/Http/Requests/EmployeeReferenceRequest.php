<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeReferenceRequest extends FormRequest
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
            'user_id' => 'required|numeric',
            'reference_name' => 'required|alpha_spaces',
            'reference_organization' => 'required|alpha_spaces',
            'reference_phone' => 'required|max:16|min:6|regex:/\+*[0-9]+$/',
        ];
    }
}
