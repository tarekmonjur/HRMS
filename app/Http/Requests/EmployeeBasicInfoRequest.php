<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeBasicInfoRequest extends FormRequest
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
//             $employee_no ='required|regex:/[0-9][\-{1}][0-9]+$/|unique:users,id,'.$this->segment(3);
             $employee_no ='required|regex:/[0-9a-zA-Z][\-{1}][0-9]+$/|unique:users,id,'.$this->segment(3);
             $email = 'required|email|unique:users,email,'.$this->segment(3);
             // $password = 'nullable';
             // $retype_pass = 'nullable';
        }else{
             $employee_no ='required|regex:/[0-9a-zA-Z][\-{1}][0-9]+$/|unique:users';
             $email = 'required|email|unique:users';
             // $password = 'nullable|min:6|max:16';
             // $retype_pass = 'nullable|same:password';
        }

        return [
            'employee_no' => $employee_no,
            'employee_type_id' => 'required|numeric',
            'from_date' => 'required_if:employee_type_id,2,4,1',
            'to_date' => 'required_if:employee_type_id,2,4',
            'branch_id' => 'required|numeric',
            'designation_id' => 'required|numeric',
            'unit_id' => 'required|numeric',
            'first_name' => 'required|alpha_spaces',
            'last_name' => 'required|alpha_spaces',
            'email' => $email,
            'mobile_number' => 'required|max:17|min:11|regex:/\+*[0-9]+$/',
            'password' => 'nullable|min:6|max:16',
            'retype_password' => 'nullable|same:password',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:4000',
            // 'present_division_id' => 'required',
            // 'present_district_id' => 'required',
            // 'present_policestation_id' => 'required',
            // 'permanent_division_id' => 'required',
            // 'permanent_district_id' => 'required',
            // 'permanent_policestation_id' => 'required',
        ];
    }


    public function attributes(){
        return [
            'designation_id' => 'employee designation',
            'unit_id' => 'employee unit',
            'employee_type_id' => 'employee type',
            'supervisor_id' => 'employee supervisor',

            // 'present_division_id' => 'division',
            // 'present_district_id' => 'district',
            // 'present_policestation_id' => 'police station',
            // 'present_postoffice' => 'post office',

            // 'permanent_division_id' => 'division',
            // 'permanent_district_id' => 'district',
            // 'permanent_policestation_id' => 'police station',
            // 'permanent_postoffice' => 'post office',
        ];
    }


    public function messages(){
        return [
            'designation_id.numeric' => 'The :attribute field is required.',
            'supervisor_id.numeric' => 'The :attribute field is required.',
            'image.max' => 'The file size must be less then 4 MB.',
            'from_date.required_if' => 'The :attribute field is required.',
            'to_date.required_if' => 'The :attribute field is required.',

        ];
    }



    
}
