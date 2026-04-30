@component('mail::message')
# Bill #{{ $bill->id }} Rejected

Hello {{ $recipientType === 'vendor' ? ($bill->vendor->name ?? 'Vendor') : 'Approver' }},

The bill **#{{ $bill->id }}** ({{ $bill->title ?? 'N/A' }}) has been **rejected** by **{{ $rejectedBy->name }}**.

@isset($bill->rejection_reason)
> **Reason:** {{ $bill->rejection_reason }}
@endisset

Please review the bill and take necessary action.

@component('mail::panel')
**Vendor:** {{ $bill->vendor->name ?? 'N/A' }}  
**Bill Amount:** ₹{{ number_format($bill->amount ?? 0, 2) }}  
**Status:** {{ ucfirst($bill->status) }}  
**Rejected By:** {{ $rejectedBy->name }}
@endcomponent

Thanks,  
**Finance Team**  
{{ config('app.name') }}
@endcomponent
