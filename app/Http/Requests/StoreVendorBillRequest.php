<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorBillRequest extends FormRequest
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
        $rules = [
            'invoice_file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB in KB
            ],
            'bill_number' => 'required_without:invoice_file|string|max:255',
            'bill_date' => 'required_without:invoice_file|date|before_or_equal:today',
            'due_date' => 'required_without:invoice_file|date|after:bill_date',
            'amount' => 'required_without:invoice_file|numeric|min:0',
            'tax_amount' => 'required_without:invoice_file|numeric|min:0',
            'total_amount' => 'required_without:invoice_file|numeric|min:0',
            'gst_type' => 'required_without:invoice_file|in:IGST,CGST,SGST',
            'gst_percentage' => 'required_without:invoice_file|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_credit_note' => 'sometimes|boolean',
            'credit_note_number' => 'required_if:is_credit_note,true|string|max:255|nullable',
            'credit_note_date' => 'required_if:is_credit_note,true|date|before_or_equal:today|nullable',
            'credit_note_reason' => 'required_if:is_credit_note,true|string|nullable',
            'credit_note_file' => [
                'required_if:is_credit_note,true',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB in KB
            ]
        ];

        // If we're in the second step (after OCR), make the fields required
        if ($this->has('ocr_processed') && $this->input('ocr_processed') === '1') {
            $rules = array_merge($rules, [
                'bill_number' => 'required|string|max:255',
                'bill_date' => 'required|date|before_or_equal:today',
                'due_date' => 'required|date|after:bill_date',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'gst_type' => 'required|in:IGST,CGST,SGST',
                'gst_percentage' => 'required|numeric|min:0|max:100',
            ]);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bill_number.required' => 'The bill number is required.',
            'bill_date.required' => 'The bill date is required.',
            'bill_date.before_or_equal' => 'The bill date cannot be in the future.',
            'due_date.required' => 'The due date is required.',
            'due_date.after' => 'The due date must be after the bill date.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'tax_amount.required' => 'The tax amount is required.',
            'tax_amount.numeric' => 'The tax amount must be a number.',
            'tax_amount.min' => 'The tax amount must be at least 0.',
            'total_amount.required' => 'The total amount is required.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.min' => 'The total amount must be at least 0.',
            'gst_type.required' => 'Please select a GST type.',
            'gst_percentage.required' => 'The GST percentage is required.',
            'gst_percentage.numeric' => 'The GST percentage must be a number.',
            'gst_percentage.min' => 'The GST percentage must be at least 0.',
            'gst_percentage.max' => 'The GST percentage cannot exceed 100.',
            'invoice_file.required' => 'Please upload an invoice file.',
            'invoice_file.mimes' => 'The invoice file must be a file of type: pdf, jpg, jpeg, png.',
            'invoice_file.max' => 'The invoice file must not exceed 5MB.',
            'credit_note_number.required_if' => 'The credit note number is required when creating a credit note.',
            'credit_note_date.required_if' => 'The credit note date is required when creating a credit note.',
            'credit_note_reason.required_if' => 'Please provide a reason for the credit note.',
            'credit_note_file.required_if' => 'Please upload the credit note file.',
            'credit_note_file.mimes' => 'The credit note file must be a file of type: pdf, jpg, jpeg, png.',
            'credit_note_file.max' => 'The credit note file must not exceed 5MB.'
        ];
    }
}
