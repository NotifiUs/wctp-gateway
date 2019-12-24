<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') &middot; {{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    @stack('scripts')

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @stack('css')

    <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="/images/favicon/site.webmanifest">

</head>
<body>
    <div id="app">
        @include('layouts.nav')
        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <!-- this needs cleaned up some, better use of templates, split out account side menu? -->
                    @if( Auth::check() )
                        @if( ! \Illuminate\Support\Str::startsWith( request()->path(), 'account' ) )
                            <div class="col-md-4">
                                @include('layouts.side')
                            </div>
                        @endif
                    @endif
                    <div class="col">
                        @yield('content')
                    </div>
                </div>
            </div>

        </main>
        @include('layouts.footer')
    </div>

    @stack('modals')
</body>
</html>
