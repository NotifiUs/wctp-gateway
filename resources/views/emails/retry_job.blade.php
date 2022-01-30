@component('mail::message')
# Retrying job

The following job did not succeed on the first attempt.

@component('mail::panel')

| Detail | Value |
|-------:|:------|
@foreach($details as $key => $value)
|  **{{ $key }}** | {{ $value }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => secure_url('/messages')])
View Messages
@endcomponent

Thanks,<br>
{{ config('app.name') }}

**Server**: {{ $host }}
@endcomponent
