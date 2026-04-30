@component('mail::message')
# Welcome {{ $vendor->name }}!

Thank you for joining our platform. We're excited to have you on board as a valued vendor.

You can access your vendor portal by clicking the button below:

@component('mail::button', ['url' => $portalUrl])
Go to Vendor Portal
@endcomponent

Once you log in for the first time, please reset your password for security purposes.

If you have any questions or need assistance, please contact our support team.

Thanks,<br>
**The Vendor Management Team**
@endcomponent
