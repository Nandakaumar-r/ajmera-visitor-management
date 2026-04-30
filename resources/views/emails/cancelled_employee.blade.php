@component('mail::message')
# Dear {{ $resignation->employee->employee_name }},

We are writing to confirm that your resignation request has been **successfully cancelled**.

**Status:** {{ $resignation->status }}

**Reason :**  {{ $resignation->resignation_reason }}

If you have any questions, please contact HR or your reporting manager.

Thanks,  
{{ config('app.name') }}
@endcomponent
