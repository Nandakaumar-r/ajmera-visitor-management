@component('mail::message')
# Last Working Day Confirmation

Dear {{ $manager->manager_name }},

The resignation for employee **{{ $resignation->employee->employee_name }}** has been accepted. The last working day has been confirmed as **{{ $resignation->manager_last_working_day }}**.

Regards,  
HR Team
@endcomponent
