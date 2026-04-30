<p>Hello {{ $candidate->name }},</p>

<p><strong>Congratulations!!! Welcome to Fidelis Group.</strong></p>

<p>It gives us immense pleasure to appoint you as <strong>{{ $candidate->orfCreation->designation }}</strong> with effect from <strong>{{ \Carbon\Carbon::parse($candidate->joining_date)->format('jS \\of F Y') }}</strong>, and your monthly CTC will be <strong>Rs. {{ number_format($candidate->salaryBreakup->ctc_month, 2) }}/-</strong>.</p>

<p>Please find attached the appointment letter with terms and conditions & the joining form.</p>

<p><strong>Kindly:</strong></p>
<ul>
    <li>Reply to this email with your acceptance.</li>
    <li>Send the duly filled Joining form along with a signed and scanned copy of the offer letter.</li>
</ul>

<p>We are confident that you will have a bright career with us.</p>

<p><strong>Note – Documents Required (within 8 days from the date of offer):</strong></p>
<ol>
    <li>Filled Joining Form and BGV Form (use the provided PDF files only).</li>
    <li>Filled Gratuity & Nomination and Declaration form.</li>
    <li>Copy of resignation and relieving letter.</li>
    <li>Attested copy of PAN Card.</li>
    <li>Passport sized photographs.</li>
    <li>PF and ESI declaration (to be submitted at time of joining).</li>
    <li>Cancelled cheque or Bank Passbook copy (mandatory for account details).</li>
    <li>Old PF/Universal Account Number (UAN) and ESIC account number (if available).</li>
    <li>Last 3 months’ payslips from previous employment.</li>
    <li>Signed Copy of Offer Letter (all pages).</li>
</ol>

<p>If you have any questions, feel free to reach out to our HR. We are here to assist you.</p>

<p>Welcome aboard!</p>

<p>Regards,<br>
<strong>HR Department</strong><br>
Fidelis Technology Services Pvt. Ltd.</p>
