@component('mail::message')
# Last Working Day Confirmation

Dear {{ $resignation->employee->employee_name }},

Your resignation has been accepted. Your last working day has been set to **{{ $resignation->manager_last_working_day }}**.

Approved by: {{ $manager->manager_name }}

Regards,  
HR Team
@endcomponent
