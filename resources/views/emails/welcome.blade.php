@component('mail::message')
# Welcome to {{ config('app.name') }}

A user account has been created for you!

Your username: **{{ $email }}**<br>
Your password: **{{ $password }}**

@component('mail::panel')
Please change your password after logging in
@endcomponent

@component('mail::button', ['url' => secure_url('/')])
Login Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}

**Server**: {{ $host }}
@endcomponent
