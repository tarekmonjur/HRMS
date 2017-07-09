<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeePersonalInfoRequest extends FormRequest
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

        if($this->segment(3)){
             $national_id ='required|digits_between:10,20|unique:employee_details,user_id,'.$this->segment(3);
             $passport_no = 'nullable|min:8|max:20|unique:employee_details,user_id,'.$this->segment(3);
             $tin_no = 'nullable|digits_between:6,20|unique:employee_details,user_id,'.$this->segment(3);
        }else{
             $national_id = 'required|digits_between:10,20|unique:employee_details';
             $passport_no = 'nullable|min:8|max:20|unique:employee_details';
             $tin_no = 'nullable|digits_between:6,20|unique:employee_details';
        }

        return [
            'father_name' => 'required|alpha_spaces_dot',
            'mother_name' => 'required|alpha_spaces_dot',
            'spouse_name' => 'required_if:marital_status,married',
            'national_id' => $national_id,
            'passport_no' => $passport_no,
            'tin_no' => $tin_no,
            'personal_email' => 'required|email',
            'official_email' => 'required|email',
            // 'phone_number' => 'required|digits_between:6,15',
            'phone_number' => 'required|max:17|min:7|regex:/^(\+?)?[0-9]+$/',
            'birth_date' => 'nullable|date',
            'joining_date' => 'required|date',
            'gender' => 'required',
            'marital_status' => 'required',
            'religion_id' => 'required',
            'nationality' => 'nullable|alpha_spaces',
            'emergency_contact_number' => 'nullable|max:17|min:7|regex:/^(\+?)?[0-9]+$/',
        ];
    }



    public function attributes(){
        return [
            'religion_id' => 'religion'
        ];
    }


}
