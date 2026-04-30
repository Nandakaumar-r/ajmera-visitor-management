@component('mail::message')
# New Bill Awaiting Your Approval

Dear {{ $approver->name }},

A new bill has been uploaded by **{{ $bill->vendor->name }}** and requires your approval.

**Bill Details:**
- Bill Number: {{ $bill->bill_number }}
- Bill Date: {{ $bill->bill_date }}
- Total Amount: ₹{{ number_format($bill->total_amount, 2) }}
- Description: {{ $bill->description ?? 'N/A' }}

@component('mail::button', ['url' => route('admin.bills.approval.show', $bill->id)])
Review Bill
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
