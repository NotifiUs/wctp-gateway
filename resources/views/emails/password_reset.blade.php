@component('mail::message')
# Your password has been reset

Your password has been reset. If this was intended, please ignore this email.

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
