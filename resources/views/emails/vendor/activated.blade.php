@component('mail::message')
# Congratulations {{ $vendor->name }},

Your documents have been verified, and your vendor account is now **active**.

You can now access and use our portal without restrictions.

@component('mail::button', ['url' => route('vendor.dashboard')])
Go to Portal
@endcomponent

Thanks,<br>
**Fidelis HRMS System**
@endcomponent
