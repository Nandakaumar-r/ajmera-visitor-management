@component('mail::message')
# 🎉 Happy Birthday, {{ $employee->name }}! 🎉

We at {{ config('app.name') }} wish you a fantastic birthday filled with joy, laughter, and wonderful memories!

@component('mail::button', ['url' => ''])
Celebrate with Us
@endcomponent

Wishing you all the best for the year ahead.

Warm regards,  
{{ config('app.name') }}
@endcomponent
