<html>
<body>
    <h1>Relieving Letter</h1>
    <p>Date: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    <p>Dear {{ $employee->employee_name }},</p>

    <p>This is to certify that <b>{{ $employee->employee_name }}</b> was employed with <b> {{ $companyName }}</b> from {{ $employee->employee_date_of_joining }} to {{ $exitProcess->last_working_day }} as a <b>{{ $employee->designation->designation_name }}</b>.</p>

    <p>We wish them success in their future endeavors.</p>

    <p>Best regards,</p>
    <p>{{ $companyName }}</p>
</body>
</html>
