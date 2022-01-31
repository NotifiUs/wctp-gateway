@component('mail::message')
# Failed login

Your password was entered incorrectly at {{ config('app.name') }}.
If this was you, please ignore this email.

@component('mail::panel')
If this wasn't you, please change your password immediately.
@endcomponent

@component('mail::button', ['url' => secure_url('/password/reset')])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}

**Server**: {{ $host }}
@endcomponent
