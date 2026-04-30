@component('mail::message')
# New Help Request

A new help request has been submitted by {{ $helpRequest->user->name }}.

**Category:** {{ $helpRequest->category }}  
**Subject:** {{ $helpRequest->subject }}  
**Priority:** {{ ucfirst($helpRequest->priority) }}

**Description:**  
{{ $helpRequest->description }}

@component('mail::button', ['url' => route('help-requests.show', $helpRequest)])
View Request
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
