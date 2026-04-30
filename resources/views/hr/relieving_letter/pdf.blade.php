<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relieving Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .date {
            text-align: right;
            margin-bottom: 30px;
        }
        .content {
            text-align: justify;
            margin-bottom: 50px;
        }
        .signature {
            margin-top: 50px;
        }
        .footer {
            margin-top: 100px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FIDELIS GROUP</h1>
    </div>

    <div class="date">
        Date: {{ $letter->letter_date->format('F d, Y') }}
    </div>

    <div class="content">
        {!! nl2br(e($letter->content)) !!}
    </div>

    <div class="signature">
        <p>For Fidelis Group,</p>
        <br><br>
        <p>_______________________</p>
        <p>{{ $letter->generatedBy->name }}</p>
        <p>HR Manager</p>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>
