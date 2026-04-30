@component('mail::message')
{{-- Logo --}}

# Hello {{ $wfhRequest->employee->name }},

Your Work From Home request from **{{ $wfhRequest->start_date->format('d M Y') }}** to **{{ $wfhRequest->end_date->format('d M Y') }}** has been **{{ ucfirst($status) }}**.

@if($status === 'rejected' && $wfhRequest->manager_comments)
**Manager Comments:**  
{{ $wfhRequest->manager_comments }}
@endif

If you have any questions, please reach out to your manager or HR.

Thank you,  
{{ config('app.name') }}

@endcomponent
