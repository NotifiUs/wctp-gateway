<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') &middot; {{ config('app.name') }}</title>

    @vite('resources/js/app.js')

    @stack('css')

    <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon.png">

</head>
<body>
<div id="app">
    @include('layouts.nav')

    @if( Auth::check() && ( constant( config('tls.protocol_support') ) < CURL_SSLVERSION_TLSv1_2 || config('tls.verify_certificates') === false )  )
        @include('layouts.tls-warning')
    @endif
    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                @if( Auth::check() )
                    @if( ! Str::startsWith( request()->path(), 'account' )
                        && ! Str::startsWith( request()->path(), 'mfa' )
                        && ! Str::startsWith( request()->path(), 'password')
                        )
                        <div class="col-md-4">
                            @include('layouts.side')
                        </div>
                        <div class="col-md-8">
                            @yield('content')
                        </div>
                    @else
                        <div class="col">
                            @yield('content')
                        </div>
                    @endif
                @else
                    <div class="col">
                        @yield('content')
                    </div>
                @endif

            </div>
        </div>

    </main>
    @include('layouts.footer')
</div>

@stack('modals')

@stack('scripts')
</body>
</html>
