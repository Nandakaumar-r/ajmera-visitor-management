@component('mail::message')
# Welcome to Nexo HR!

Dear **{{ $employeeName }}**,

We're excited to welcome you to Nexo HR! Your account has been successfully created, and you can now access our HR Management System. Below are your login credentials:

@component('mail::panel')
**Login Details**
- **URL:** {{ $url }}
- **Email:** {{ $email }}
- **Password:** {{ $password }}
@endcomponent

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Login to Your Account
@endcomponent

For security reasons, we strongly recommend changing your password after your first login. If you experience any issues or need assistance, please contact your HR representative.

@component('mail::table')
| Important Security Notes |
| ----------------------- |
| • Change your password immediately after first login |
| • Keep your credentials confidential |
| • Never share your password with anyone |
| • Ensure you're using a secure connection |
@endcomponent

Best regards,  
The Nexo HR Team

---
*This is an automated message. Please do not reply to this email.*
@endcomponent
