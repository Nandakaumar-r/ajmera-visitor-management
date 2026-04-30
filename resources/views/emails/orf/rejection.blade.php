@component('mail::message')
# Candidate Rejection Notification

The candidate **{{ $orf->name }}** has been **rejected** during the **{{ strtoupper($role) }}** review stage.

**Email:** {{ $orf->email }}  
**Mobile:** {{ $orf->mobile }}  
**Stage:** {{ strtoupper($role) }}

### Rejection Reason:
{{ $reason }}

@component('mail::button', ['url' => url('/candidate-details/show/' . $orf->id)])
View Candidate
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
