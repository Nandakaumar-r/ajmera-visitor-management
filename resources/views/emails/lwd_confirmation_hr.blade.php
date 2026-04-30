@component('mail::message')
# Resignation Accepted

Dear HR Team,

The resignation for **{{ $resignation->employee->employee_name }}** (Employee ID: {{ $resignation->employee->employee_id }}) has been accepted by Manager **{{ $manager->manager_name }}**.

**Manager Assigned Last Working Day:** {{ $resignation->manager_last_working_day }}

**Approved By:** {{ $manager->manager_name }}

Please proceed with the necessary exit formalities.

Thanks,  
{{ config('app.name') }}
@endcomponent
