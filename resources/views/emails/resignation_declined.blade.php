@component('mail::message')
# Resignation Rejected

Dear {{ $employee->employee_name }},

We regret to inform you that your resignation request has been **rejected** by your manager, {{ $manager->manager_name }}.

**Reason :**  {{ $resignation->resignation_reason }}

If you have any questions, please discuss with your manager.

---

Thanks,  
{{ config('app.name') }}
@endcomponent
