<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeInsuranceRequest extends FormRequest
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
            'employee_id' => 'required|string|max:50|unique:employee_insurances,employee_id',
            'employee_name' => 'required|string|max:255',
            
            // Spouse fields - all required if any one is provided
            'spouse_name' => 'nullable|required_with:spouse_dob,spouse_aadhar,spouse_gender|string|max:255',
            'spouse_dob' => 'nullable|required_with:spouse_name|date|before:today',
            'spouse_aadhar' => 'nullable|required_with:spouse_name|digits:12',
            'spouse_gender' => 'nullable|required_with:spouse_name|in:Male,Female,Other',
            
            // Child 1 fields
            'child1_name' => 'nullable|required_with:child1_dob,child1_aadhar,child1_gender|string|max:255',
            'child1_dob' => 'nullable|required_with:child1_name|date|before:today',
            'child1_aadhar' => 'nullable|required_with:child1_name|digits:12',
            'child1_gender' => 'nullable|required_with:child1_name|in:Male,Female,Other',
            
            // Child 2 fields
            'child2_name' => 'nullable|required_with:child2_dob,child2_aadhar,child2_gender|string|max:255',
            'child2_dob' => 'nullable|required_with:child2_name|date|before:today',
            'child2_aadhar' => 'nullable|required_with:child2_name|digits:12',
            'child2_gender' => 'nullable|required_with:child2_name|in:Male,Female,Other',
            
            'premium' => 'required|numeric|min:645',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required',
            'employee_id.unique' => 'This Employee ID has already been submitted',
            'spouse_aadhar.digits' => 'Aadhar number must be exactly 12 digits',
            'child1_aadhar.digits' => 'Aadhar number must be exactly 12 digits',
            'child2_aadhar.digits' => 'Aadhar number must be exactly 12 digits',
            '*.before' => 'Date of birth must be in the past',
        ];
    }
}
