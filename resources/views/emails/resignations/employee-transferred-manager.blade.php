@component('mail::message')
# Employee Transfer Confirmation

Dear {{ $resignation->employee->manager->manager_name }},

Please be informed that the employee **{{ $resignation->employee->employee_name }}** has been successfully transferred to the **{{ $resignation->employee->department->department_name }}** department, effective from **{{ \Carbon\Carbon::now()->format('d-m-Y') }}**.

Kindly update your records accordingly.

Regards,  
HR Team
@endcomponent
