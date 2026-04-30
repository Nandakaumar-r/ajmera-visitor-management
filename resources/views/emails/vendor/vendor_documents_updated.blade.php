@component('mail::message')
# Vendor Details Updated

Dear {{ $approver->name ?? 'Approver' }},

The vendor **{{ $vendor->name }}** has updated their profile and documents for your review.

**Vendor Details:**
- **Vendor Name:** {{ $vendor->name }}
- **Email:** {{ $vendor->email }}
- **Phone:** {{ $vendor->phone ?? 'N/A' }}
- **City:** {{ $vendor->city ?? 'N/A' }}
- **State:** {{ $vendor->state ?? 'N/A' }}
- **PAN Number:** {{ $vendor->pan_number ?? 'N/A' }}
- **GST Number:** {{ $vendor->gst_number ?? 'N/A' }}

Please log in to the admin portal to verify and approve the updated details.

@component('mail::button', ['url' => url('/admin/vendors/' . $vendor->id)])
View Vendor Details
@endcomponent

Thanks & Regards,  
**Vendor Management System**  
{{ config('app.name') }}
@endcomponent
