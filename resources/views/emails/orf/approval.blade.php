@component('mail::message')
# ORF Approved by {{ ucfirst($role) }}

The candidate <strong>{{ $orf->name }}</strong> has been approved by <strong>{{ strtoupper($role) }}</strong>.

@component('mail::button', ['url' => config('app.url') . '/orf/' . $nextRole])
View ORF
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
