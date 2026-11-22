<!DOCTYPE html>
<html class="no-js page__error" lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title') - {{ config('other.title') }}</title>
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="Error" />
        <meta property="og:title" content="{{ config('other.title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ url('/img/og.png') }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        @vite('resources/sass/main.scss')
    </head>
    <body>
        <main>
            <article>
                <section class="error">
                    <h1 class="error__heading">@yield('title')</h1>
                    <div class="error__body">@yield('description')</div>
                    <a href="{{ url('/') }}" class="error__home-link">
                        <i class="{{ config('other.font-awesome') }} fa-home"></i>
                        Go home
                    </a>
                </section>
            </article>
        </main>
    </body>
</html>
