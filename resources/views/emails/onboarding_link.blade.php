<!DOCTYPE html>
<html>
<head>
    <title>Onboarding Form</title>
</head>
<body>
    <p>Dear {{ $candidateName }},</p>

    <p>Welcome! Please complete your onboarding by clicking the link below:</p>

    <p>
        <a href="{{ $link }}" target="_blank">Click here to fill out the onboarding form</a>
    </p>

    <p>Thanks,<br>Your HR Team</p>
</body>
</html>
