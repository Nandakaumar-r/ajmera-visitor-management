@component('mail::message')
# 📌 New Resignation Submitted

Dear {{ $resignation->employee->manager->manager_name ?? 'Manager' }},

A new resignation request has been submitted.

---

**👤 Employee Details:**  
**ID:** {{ $resignation->employee_id }}  
**Name:** {{ $resignation->employee->employee_name }}  
**Email:** {{ $resignation->employee->employee_email ?? 'N/A' }}  
**Department:** {{ $resignation->employee->department->department_name ?? 'N/A' }}  
**Designation:** {{ $resignation->employee->designation->designation_name ?? 'N/A' }}

---

**📝 Resignation Details:**  
**Reason:** {{ $resignation->reason }}  
@if($resignation->additional_details)
**Additional Details:**  {{ $resignation->additional_details }}
@endif

**Proposed Last Working Day:** {{ \Carbon\Carbon::parse($resignation->resignation_date)->format('d F, Y') }}

**Submitted On:** {{ \Carbon\Carbon::parse($resignation->created_at)->format('d F, Y H:i A') }}

---

Please review the resignation and take appropriate action in the portal.

Thanks,  
{{ config('app.name') }}
@endcomponent
