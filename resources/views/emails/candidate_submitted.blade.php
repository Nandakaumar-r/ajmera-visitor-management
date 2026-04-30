<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Onboarding Candidate</title>
</head>

<body>
    <h2>New Onboarding Candidate Submitted</h2>
    <p><strong>Name:</strong> {{ $candidate['name'] }}</p>
    <p><strong>Email:</strong> {{ $candidate['email'] }}</p>
    <p><strong>Mobile:</strong> {{ $candidate['mobile'] }}</p>
    <p><strong>DOB:</strong> {{ $candidate['dob'] }}</p>
    <p>
         <a href="{{ config('app.url') . '/orf/hrbp' }}" target="_blank">
    Click here to review the candidate in the HRMS Portal
</a>
    </p>
</body>

</html>