@component('mail::message')
# HR Approved Last Working Day

Dear {{ $manager->manager_name ?? 'Manager' }},

The HR team has approved the last working day for **{{ $employee->employee_name }}** (ID: {{ $employee->employee_id }}).

**Approved Last Working Day:** {{ $resignation->exitProcess->last_working_day ?? 'N/A' }}

Regards,  
HR Department
@endcomponent
