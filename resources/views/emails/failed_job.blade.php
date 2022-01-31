@component('mail::message')
    # A job has failed!

    Please review the job failure below.

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
