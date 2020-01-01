<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') &middot; {{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/tailwind.css') }}" rel="stylesheet">
    @stack('css')

    <link rel="apple-touch-icon" sizes="180x180"  href="/images/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32"  href="/images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon.png">

</head>
<body class="bg-gray-100">
<div id="app">
    <main class="py-4">
        <div class="container mx-auto text-center">
            <h3 class="text-3xl uppercase my-4 font-black text-gray-700">@yield('message')</h3>
            <a class="inline-block"  alt="@yield('title')" title="@yield('link')" href="@yield('link')">
                <img class="max-w-lg mx-auto my-4" src="@yield('image')" title="@yield('title')" alt="@yield('title')">
           </a>
        </div>

    </main>
    @include('layouts.footer')
</div>

@stack('modals')

<script src="{{ mix('js/font-awesome.js') }}"></script>
@stack('scripts')
</body>
</html>
