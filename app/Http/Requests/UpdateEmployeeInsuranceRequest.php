<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeInsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employee_insurances', 'employee_id')->ignore($this->route('insurance'))
            ],
            'employee_name' => 'required|string|max:255',
            
            'spouse_name' => 'nullable|string|max:255',
            'spouse_dob' => 'nullable|date|before:today',
            'spouse_aadhar' => 'nullable|digits:12',
            'spouse_gender' => 'nullable|in:Male,Female,Other',
            
            'child1_name' => 'nullable|string|max:255',
            'child1_dob' => 'nullable|date|before:today',
            'child1_aadhar' => 'nullable|digits:12',
            'child1_gender' => 'nullable|in:Male,Female,Other',
            
            'child2_name' => 'nullable|string|max:255',
            'child2_dob' => 'nullable|date|before:today',
            'child2_aadhar' => 'nullable|digits:12',
            'child2_gender' => 'nullable|in:Male,Female,Other',
            
            'premium' => 'required|numeric|min:645',
            'status' => 'nullable|in:pending,approved,rejected',
        ];
    }
}
