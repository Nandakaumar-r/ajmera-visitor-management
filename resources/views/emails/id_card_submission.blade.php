<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Submission Notification</title>
</head>
<body>
    <h1>ID Card Submission Notification</h1>
    <p>Employee ID: {{ $employee_id }}</p>
    <p>File Path: <a href="{{ asset('storage/' . $file_path) }}">{{ 'storage/' . $file_path }}</a></p>
    <p>Remarks: {{ $remarks }}</p>
</body>
</html>
