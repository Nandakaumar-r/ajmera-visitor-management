@component('mail::message')
# Payment Status Update

Dear **{{ $vendor->name }}**,

We would like to inform you that the payment for your bill **#{{ $bill->id }}** has been updated.

**Bill Details:**
- **Bill ID:** {{ $bill->id }}
- **Amount:** ₹{{ number_format($bill->amount, 2) }}
- **Status:** {{ ucfirst($status) }}
- **Processed On:** {{ \Carbon\Carbon::parse($bill->updated_at)->format('d M Y, h:i A') }}

@if(!empty($bill->payment_notes))
**Payment Notes:**  
{{ $bill->payment_notes }}
@endif

@if($status === 'Paid')
Your payment has been successfully transferred. Please check your account for confirmation.
@elseif($status === 'Processing')
Your payment is currently being processed. You’ll receive another update once completed.
@else
Unfortunately, your payment has failed. Please contact our finance team for clarification.
@endif

Thanks & Regards,  
**Finance Team**  
{{ config('app.name') }}

@component('mail::button', ['url' => url('/vendor/bills/' . $bill->id)])
View Bill
@endcomponent

@endcomponent
