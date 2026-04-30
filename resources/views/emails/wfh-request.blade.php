@component('mail::message')
{{-- Logo --}}

# 🏠 Work From Home Request

A new WFH request has been submitted and requires your attention.

---

**Employee Name:** {{ $wfhRequest->employee->name }}

**Employee ID:** {{ $wfhRequest->employee_id ?? 'N/A' }}

**Request Status:**  <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; background-color: #ffc107; color: #212529;">
  {{ ucfirst($wfhRequest->status) }}
</span>

**Start Date:**  {{ $wfhRequest->start_date->format('d M Y') }}

**End Date:**  {{ $wfhRequest->end_date->format('d M Y') }}

**Duration:**  {{ $wfhRequest->duration }} day(s)

**Reason:**  {{ $wfhRequest->reason }}

**Work Location:**  {{ $wfhRequest->work_location }}

**Emergency Contact:**  {{ $wfhRequest->emergency_contact }}

**Internet Speed:**  {{ $wfhRequest->internet_speed }} Mbps

@if($wfhRequest->equipment_needed)
**Equipment Needed:**  {{ $wfhRequest->equipment_needed }}
@endif

**Backup Plan:**  {{ $wfhRequest->backup_plan }}

**Submitted On:**  {{ $wfhRequest->created_at->format('d M Y, h:i A') }}

---
<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td align="center">
      <a href="{{ route('wfh.approve.confirm', $wfhRequest->id) }}" 
         style="background-color: #28a745; border: solid 1px #28a745; border-radius: 4px; color: #ffffff; display: inline-block; padding: 10px 20px; text-decoration: none; font-weight: bold;">
        ✓ Approve
      </a>
    </td>
    <td width="20"></td> <!-- Space between buttons -->
    <td align="center">
      <a href="{{ route('wfh.reject.confirm', $wfhRequest->id) }}" 
         style="background-color: #dc3545; border: solid 1px #dc3545; border-radius: 4px; color: #ffffff; display: inline-block; padding: 10px 20px; text-decoration: none; font-weight: bold;">
        ✗ Reject
      </a>
    </td>
  </tr>
</table>


---

**Note:** This is an automated notification. Please review the request and take appropriate action.

If you have any questions, please contact HR at [{{ config('mail.hr_email', 'hr@company.com') }}](mailto:{{ config('mail.hr_email', 'hr@company.com') }})

This email was sent on {{ now()->format('d M Y, h:i A') }}.

Thanks,  
{{ config('app.name') }}
@endcomponent
