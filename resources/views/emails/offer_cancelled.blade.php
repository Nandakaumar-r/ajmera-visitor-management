@component('mail::message')
# Hello {{ $orf->name }},

We regret to inform you that your application with **Fidelis Group** has been **cancelled**.

@isset($reason)
### Reason:
{{ $reason }}
@endisset

We appreciate your interest in being part of our team. Please don't be discouraged — we encourage you to apply for future opportunities that match your profile.

If you have any questions or need clarification, feel free to reach out.

@component('mail::button', ['url' => 'mailto:hr@fidelisgroup.in'])
Contact HR
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
