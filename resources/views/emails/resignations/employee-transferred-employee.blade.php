@component('mail::message')
# Employee Transfer Confirmation

Dear {{ $resignation->employee->employee_name }},

This is to inform you that your manager, **{{ $resignation->employee->manager->manager_name }}**, has been successfully updated in our records. You have been transferred to the **{{ $resignation->employee->department->department_name }}** department, effective from **{{ \Carbon\Carbon::now()->format('d-m-Y') }}**.

If you have any questions or need further assistance, feel free to contact HR.

Regards,  
HR Team
@endcomponent
