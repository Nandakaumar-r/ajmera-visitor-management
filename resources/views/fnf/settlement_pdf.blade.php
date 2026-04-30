<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F&F Settlement</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Include Tailwind CSS if needed -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        h1, h2 {
            color: #333;
        }
        .details {
            margin-top: 20px;
        }
        .details p {
            margin: 5px 0;
        }
        .summary {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .summary h3 {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-2xl font-bold">Full and Final Settlement</h1>
        <p><strong>Employee Name:</strong> {{ $resignation->employee->name }}</p>
        <p><strong>Employee ID:</strong> {{ $resignation->employee->id }}</p>
        <p><strong>Resignation ID:</strong> {{ $resignation->id }}</p>
        <p><strong>Designation:</strong> {{ $resignation->employee->designation->designation_name }}</p>
        <p><strong>Department:</strong> {{ $resignation->employee->department->department_name }}</p>

        <h2 class="mt-4">Settlement Details:</h2>
        <div class="details">
            <p><strong>Basic Salary:</strong> &#8377;{{ number_format($fnfSettlement->basic_salary, 2) }}</p>
            <p><strong>Days Worked:</strong> {{ $fnfSettlement->days_worked }}</p>
            <p><strong>Proportionate Salary:</strong> &#8377;{{ number_format($fnfSettlement->proportionate_salary, 2) }}</p>
            <p><strong>Unused Leaves:</strong> &#8377;{{ number_format($fnfSettlement->unused_leaves, 2) }}</p>
            <p><strong>Leave Encashment:</strong> &#8377;{{ number_format($fnfSettlement->leave_encashment, 2) }}</p>
            <p><strong>Gratuity:</strong> &#8377;{{ number_format($fnfSettlement->gratuity, 2) }}</p>
            <p><strong>Bonus:</strong> &#8377;{{ number_format($fnfSettlement->bonus, 2) }}</p>
            <p><strong>Incentives:</strong> &#8377;{{ number_format($fnfSettlement->incentives, 2) }}</p>
            <p><strong>Tax Deduction:</strong> &#8377;{{ number_format($fnfSettlement->tax_deduction, 2) }}</p>
            <p><strong>Loan Balance:</strong> &#8377;{{ number_format($fnfSettlement->loan_balance, 2) }}</p>
            <p><strong>Notice Recovery:</strong> &#8377;{{ number_format($fnfSettlement->notice_recovery, 2) }}</p>
            <p><strong>Other Deductions:</strong> &#8377;{{ number_format($fnfSettlement->other_deductions, 2) }}</p>
            <p><strong>Total Earnings:</strong> &#8377;{{ number_format($fnfSettlement->total_earnings, 2) }}</p>
            <p><strong>Total Deductions:</strong> &#8377;{{ number_format($fnfSettlement->total_deductions, 2) }}</p>
            <p><strong>Net Payable:</strong> &#8377;{{ number_format($fnfSettlement->net_payable, 2) }}</p>
        </div>

        <div class="summary">
            <h3>Summary of Settlement:</h3>
            <p><strong>Status:</strong> {{ $fnfSettlement->status }}</p>
            <p><strong>Processed By:</strong> {{ $fnfSettlement->processed_by }}</p>
            <p><strong>Remarks:</strong> {{ $fnfSettlement->remarks }}</p>
            <p><strong>Processed At:</strong> {{ $fnfSettlement->processed_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
