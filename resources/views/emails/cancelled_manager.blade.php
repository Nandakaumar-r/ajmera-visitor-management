@component('mail::message')
# Dear {{ $manager->manager_name }},

Please be informed that **{{ $resignation->employee->employee_name }}** has revoked their resignation and will continue employment.

**Status:** {{ $resignation->status }}

**Reason :**  {{ $resignation->resignation_reason }}

Please update any records if necessary.

Thanks,  
{{ config('app.name') }}
@endcomponent
