@component('mail::message')
# Congratulations {{ $orf->name }}!

We are pleased to inform you that you have been **offered a position** at **Fidelis Group**.

Here are some key details:

- **Position:** {{ $orf->designation ?? 'N/A' }}
- **CTC:** {{ $orf->candidate_ctc ?? 'N/A' }}
- **Date of Joining:** {{ $orf->date_of_joining ?? 'To be Confirmed' }}

Our team will contact you shortly with your formal offer letter and onboarding steps.

@component('mail::button', ['url' => 'mailto:hr@fidelisgroup.in'])
Contact HR
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
