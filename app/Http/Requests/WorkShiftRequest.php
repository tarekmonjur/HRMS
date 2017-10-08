<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkShiftRequest extends FormRequest
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

        if($this->has('work_shift_status')){
            return [];
        }else{
            return [
                'shift_name' => 'required|alpha_spaces|min:2|max:100',
                'shift_start_time' => 'required',
                'shift_end_time' => 'required',
                // 'late_count_time' => 'required',
            ];
        }
    }
}
