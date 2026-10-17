<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8" />
        @section('title')
        <title>{{ config('other.title') }} - {{ config('other.subTitle') }}</title>
        @show

        <meta name="description" content="{{ config('other.meta_description') }}" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="_base_url" content="{{ route('home.index') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta property="og:title" content="{{ __('auth.login') }}" />
        <meta property="og:site_name" content="{{ config('other.title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ url('/img/og.png') }}" />
        <meta property="og:description" content="{{ config('unit3d.powered-by') }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:locale" content="{{ config('app.locale') }}" />

        @yield('meta')

        <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        @vite('resources/sass/pages/_auth.scss')
        @vite('resources/js/app.js')
        @livewireScriptConfig(['nonce' => HDVinnie\SecureHeaders\SecureHeaders::nonce()])
    </head>
    <body>
        @if (Session::has('errors'))
            <div id="ERROR_COPY" style="display: none">
                @foreach ($errors->getBags() as $bag)
                    @foreach ($bag->getMessages() as $errors)
                        @foreach ($errors as $error)
                            {{ $error }}
                            <br />
                        @endforeach
                    @endforeach
                @endforeach
            </div>
        @endif

        <main class="@yield('page')">
            @yield('content')
        </main>

        @foreach (['warning', 'success', 'info'] as $key)
            @if (Session::has($key))
                <script
                    nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}"
                    type="module"
                >
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                    });

                    Toast.fire({
                        icon: '{{ $key }}',
                        title: '{{ Session::get($key) }}',
                    });
                </script>
            @endif
        @endforeach

        @if (Session::has('errors'))
            <script
                nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}"
                type="module"
            >
                Swal.fire({
                    title: '<strong style=" color: rgb(17,17,17);">Error</strong>',
                    icon: 'error',
                    html: document.getElementById('ERROR_COPY').innerHTML,
                    showCloseButton: true,
                    willOpen: function (el) {
                        el.querySelector('textarea').remove();
                    },
                });
            </script>
        @endif
    </body>
</html>
