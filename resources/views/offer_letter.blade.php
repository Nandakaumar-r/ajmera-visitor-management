<!DOCTYPE html>
<html>

<head>
    <title>Offer Letter</title>
    <style>
        @page {
            margin: 140px 50px 120px 50px;
            /* Increased top margin for logo space */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.8;
            margin: 0;
            position: relative;
            padding-top: 80px;
            /* Push body content below the logo */
        }

        .header-logo {
            position: fixed;
            top: 20px;
            /* Place within printable area */
            left: 20px;
            width: 120px;
            height: auto;
            z-index: 1;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            /* Slightly lighter */
            z-index: -1;
        }

        .watermark img {
            width: 600px;
            /* Increased size */
            filter: grayscale(100%);
            /* Convert image to black & white */
            opacity: 2;
            /* Add a bit more visibility */
        }

        .footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 100px;
        }

        .footer img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            display: block;
        }
    </style>
</head>
<!-- Watermark in center -->
<div class="watermark">
    <img src="{{ public_path('images/fidelis_logo.jpg') }}" alt="Watermark">
</div>

<!-- Footer on each page -->
<div class="footer">
    <img src="{{ public_path('images/offer_footer1.png') }}" alt="Footer Image">
</div>

<!-- Header logo in top-right -->
<img src="{{ public_path('images/fidelis_logo.jpg') }}" class="header-logo" alt="Header Logo">

<body>
    <h2>Offer Letter</h2>

    <p>Date: {{ now()->format('d-m-Y') }}</p>

    <p>Mr. {{ $candidate->name }}</p>

    <p>Dear {{ $candidate->name }},</p>

    <p><strong>Subject: Appointment as {{ $candidate->orfCreation->designation }}</strong></p>

    <p>
        Further to our earlier letter, the Management is pleased to appoint you as <strong>{{ $candidate->orfCreation->designation }}</strong>
        with effect from <strong>{{ \Carbon\Carbon::parse($candidate->orfCreation->joining_date)->format('d-m-Y') }}</strong>.
    </p>

    <p>
        Your Monthly compensation will be Rs. <strong>{{ number_format($candidate->salaryBreakup->ctc_month, 2) }}</strong>/- on the basis of Cost to Company which includes the following:
    </p>

    <ul>
        <li>Provident Fund benefits by the Company.</li>
    </ul>

    <p>Please return the duplicate copy of this letter duly signed in token of your acceptance along with the following documents:</p>

    <ol>
        <li>Copies of Educational/Technical Courses</li>
        <li>Copy of PAN Card</li>
        <li>Copy of Passport</li>
        <li>Passport sized photographs</li>
        <li>Last pay slip</li>
        <li>Resignation copy and relieving letter</li>
        <li>PF and ESI declaration to be submitted at the time of joining</li>
        <li>Reference details – subject to satisfactory reference check</li>
    </ol>

    <p>
        Your appointment is subject to submission of the above-mentioned documents and successful background verification.
        We welcome you to the <strong>Fidelis</strong> family and hope for a long and happy association.
    </p>
    <p><strong>Welcome aboard!</strong></p>

    <p>Sincerely,<br><br>
        For Fidelis Technology Services Pvt. Ltd.,<br><br>

        {{-- Signature Image --}}
        <img src="{{ public_path('images/sign.png') }}" alt="Signature" style="width: 150px;"><br>

        <strong>Authorized Signatory</strong>
    </p>
    <div class="section-title">Annexure – A</div>

    <p>
        <strong>Employee Name:</strong> {{ $candidate->name }}<br>
        <strong>Designation:</strong> {{ $candidate->designation }}<br>
        <strong>Date of Appointment:</strong> {{ \Carbon\Carbon::parse($candidate->joining_date)->format('d-m-Y') }}
    </p>

    <h3>Salary Break-up</h3>

    <table style="width:100%; border-collapse: collapse;" border="1" cellpadding="8">
        <thead>
            <tr style="font-weight:bold; text-align:center;">
                <td colspan="3">Salary Break-up:</td>
            </tr>
            <tr style="font-weight:bold;">
                <td>Details</td>
                <td>Per Month (Rs)</td>
                <td>Per Annum (Rs)</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic</td>
                <td>{{ number_format($salary->basic_month, 2) }}</td>
                <td>{{ number_format($salary->basic_annual, 2) }}</td>
            </tr>
            <tr>
                <td>HRA</td>
                <td>{{ number_format($salary->hra_month, 2) }}</td>
                <td>{{ number_format($salary->hra_annual, 2) }}</td>
            </tr>
            <tr>
                <td>Statutory Bonus</td>
                <td>{{ number_format($salary->statutory_bonus_month, 2) }}</td>
                <td>{{ number_format($salary->statutory_bonus_annual, 2) }}</td>
            </tr>
            <tr>
                <td>Special Allowance</td>
                <td>{{ number_format($salary->special_allowance_month, 2) }}</td>
                <td>{{ number_format($salary->special_allowance_annual, 2) }}</td>
            </tr>

            <tr style="font-weight:bold;">
                <td colspan="3">Gross Pay</td>
            </tr>
            <tr>
                <td colspan="1">Gross Pay</td>
                <td>{{ number_format($salary->gross_pay_month, 2) }}</td>
                <td>{{ number_format($salary->gross_pay_annual, 2) }}</td>
            </tr>

            <tr>
                <td>Empl PF</td>
                <td>{{ number_format($salary->empl_pf_month, 2) }}</td>
                <td>{{ number_format($salary->empl_pf_annual, 2) }}</td>
            </tr>
            <tr>
                <td>PT</td>
                <td>{{ number_format($salary->pt_month, 2) }}</td>
                <td>{{ number_format($salary->pt_annual, 2) }}</td>
            </tr>
            <tr>
                <td>LWF</td>
                <td>{{ number_format($salary->lwf_month, 2) }}</td>
                <td>{{ number_format($salary->lwf_annual, 2) }}</td>
            </tr>

            <tr style="font-weight:bold;">
                <td colspan="3">Take Home</td>
            </tr>
            <tr>
                <td>Take Home</td>
                <td>{{ number_format($salary->take_home_month, 2) }}</td>
                <td>{{ number_format($salary->take_home_annual, 2) }}</td>
            </tr>

            <tr>
                <td>Empr PF</td>
                <td>{{ number_format($salary->empr_pf_month, 2) }}</td>
                <td>{{ number_format($salary->empr_pf_annual, 2) }}</td>
            </tr>
            <tr>
                <td>Medical Insurance</td>
                <td>{{ number_format($salary->medical_insurance_month, 2) }}</td>
                <td>{{ number_format($salary->medical_insurance_annual, 2) }}</td>
            </tr>
            <tr>
                <td>Gratuity</td>
                <td>{{ number_format($salary->gratuity_month, 2) }}</td>
                <td>{{ number_format($salary->gratuity_annual, 2) }}</td>
            </tr>
            <tr>
                <td>LWF (Employer)</td>
                <td>{{ number_format($salary->empr_lwf_month, 2) }}</td>
                <td>{{ number_format($salary->empr_lwf_annual, 2) }}</td>
            </tr>

            <tr style="font-weight:bold;">
                <td colspan="3">Cost to Company</td>
            </tr>
            <tr>
                <td>CTC</td>
                <td>{{ number_format($salary->ctc_month, 2) }}</td>
                <td>{{ number_format($salary->ctc_annual, 2) }}</td>
            </tr>
        </tbody>
    </table>


    <p><strong>Note:</strong> CTC includes all allowances and statutory components (Employer & Employee contribution of PF, PT, and Bonus paid as advance on monthly basis).</p>
    <p>
        Salary is subject to Income Tax deduction (if applicable) and will be credited to the provided bank account.
        Salary is strictly confidential. Salary slips will be emailed monthly.
    </p>

    <div class="signature">
        <div>
            ___________________________<br>
            Authorized Signatory
        </div>
        <div>
            ___________________________<br>
            Acceptance by the Employee
        </div>
    </div>

    <div class="section-title">Employment Terms and Conditions</div>

    <p>{!! nl2br(e(view('partials.employment_terms')->render())) !!}</p>
</body>

</html>