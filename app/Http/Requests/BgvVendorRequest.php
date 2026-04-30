<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BgvVendorRequest extends FormRequest
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
        $vendorId = $this->route('vendor') ? $this->route('vendor')->id : null;
        
        return [
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:bgv_vendors,email,' . $vendorId,
            'phone' => 'required|string|max:20',
            'escalation_person' => 'nullable|string|max:255',
            'escalation_email' => 'nullable|email|max:255',
            'escalation_phone' => 'nullable|string|max:20',
            'tat_days' => 'required|integer|min:1|max:365',
            'cost_structure' => 'required|array',
            'cost_structure.education' => 'required|numeric|min:0',
            'cost_structure.employment' => 'required|numeric|min:0',
            'cost_structure.criminal' => 'required|numeric|min:0',
            'cost_structure.id' => 'required|numeric|min:0',
            'cost_structure.address' => 'required|numeric|min:0',
            'cost_structure.database' => 'required|numeric|min:0',
            'cost_structure.uan' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'cost_structure.education.required' => 'Education check cost is required.',
            'cost_structure.employment.required' => 'Employment check cost is required.',
            'cost_structure.criminal.required' => 'Criminal check cost is required.',
            'cost_structure.id.required' => 'ID check cost is required.',
            'cost_structure.address.required' => 'Address check cost is required.',
            'cost_structure.database.required' => 'Database check cost is required.',
        ];
    }
}
