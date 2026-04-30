@component('mail::message')
# Resignation Acknowledgment

Dear {{ $resignation->employee->employee_name }},

We acknowledge the receipt of your resignation dated **{{ $formattedResignationDate }}**.

**Reason:** {{ $resignation->reason }}

@if($resignation->additional_details)
**Additional Details:** {{ $resignation->additional_details }}
@endif

Our team will reach out with further information regarding the exit process. Should you have any questions, feel free to contact HR.

Regards,  
HR Team
@endcomponent
