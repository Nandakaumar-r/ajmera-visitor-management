<!DOCTYPE html>
<html>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f6f6f6;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0; background-color: #f6f6f6;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

                    <tr>
                        <td style="padding: 0 30px 20px 30px; color: #555; font-size: 16px; line-height: 1.6; margin-top: 10px">
                            <p>
                                <br><br>
                                <strong>Hi All,</strong><br><br>
                                We’re pleased to inform you that the reimbursement claims for IT Internal employees for the month of <strong>{{ $month }}</strong>, have been successfully processed. The approved amounts will be credited to the respective employee accounts shortly.
                            </p>
                            <!-- <p>In case of any bank rejections, the Reimbursement Team will be notified, and the claims will be re-initiated upon receipt of the updated bank details. Otherwise, please consider the claims as successfully processed.</p> -->
                        </td>
                    </tr>

                    <!-- Button -->
                    <tr>
                        <td style="text-align: center; padding: 20px;">
                             <a href="{{ url('/travel/reimbursements/processed') }}"
                               style="background-color: #28a745; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-size: 16px; font-weight: bold;">
                                Pease Click Here To Review The Reimbursement
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; text-align: center; color: #888; font-size: 14px;">
                            Best regards,<br>
                            <strong>Finance Team</strong>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
