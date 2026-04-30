@component('mail::message')
# HR Approved Last Working Day

Dear {{ $employee->employee_name }},

Your resignation process has been updated and the HR team has approved your last working day.

**Approved Last Working Day:** {{ $resignation->exitProcess->last_working_day ?? 'N/A' }}

Please coordinate with your manager for any pending tasks.

Regards,  
HR Department
@endcomponent
