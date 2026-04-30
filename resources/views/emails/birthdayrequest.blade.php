@component('mail::message')
# Hi {{ $user->name }},

Your special day is almost here! 🎉 Please reply to this email with a recent photo so we can include it in your birthday celebration.

Best,
{{ config('app.name') }}
@endcomponent
