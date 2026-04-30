<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F&F Settlement Notification</title>
</head>
<body>
    <h1>F&F Settlement Notification</h1>
    <p>A new Full and Final settlement has been generated.</p>
    <p>Details:</p>
    <ul>
        <li>Employee Name: {{ $fnfSettlement->resignation->employee->name }}</li>
        <li>Resignation ID: {{ $fnfSettlement->resignation_id }}</li>
        <li>Net Payable: {{ $fnfSettlement->net_payable }}</li>
    </ul>
    <p>Please find the attached PDF for more details.</p>
</body>
</html>
