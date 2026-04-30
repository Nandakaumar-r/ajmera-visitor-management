@component('mail::message')
# Last Working Day Confirmation

Dear {{ $employee->name }},

Your Last Working Day (LWD) has been confirmed.

**LWD:** {{ $employee->last_working_day }}

If you have any questions, please contact HR or your manager.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
