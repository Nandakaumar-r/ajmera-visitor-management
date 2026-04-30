<!DOCTYPE html>
<html>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f6f6f6;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0; background-color: #f6f6f6;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

                    <tr>
                        <td style="padding: 30px; color: #555; font-size: 16px; line-height: 1.6;">
                            <p>
                                Hi,<br><br>
                                The reimbursement claims for IT Internal employees for the month of <strong>{{ $monthYear }}</strong> have been duly reviewed and approved by HR and verified by Finance. Everything appears to be in order.<br><br>
                                We now seek your final approval to proceed with the release of payments.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: center; padding: 20px;">
                            <a href="{{ url('/travel/reimbursements/accountant') }}"
                                style="background-color: #28a745; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-size: 16px;">
                                Please Click Here To Review The Reimbursement
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 30px; text-align: center; color: #888; font-size: 14px;">
                            Best regards,<br>
                            <strong>Team HR</strong>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
