<meta charset="UTF-8" />
@section('title')
<title>{{ config('other.title') }} - {{ config('other.subTitle') }}</title>
@show

<meta name="description" content="{{ config('other.meta_description') }}" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="_base_url" content="{{ route('home.index') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

@yield('meta')

<link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
<link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />

@if (auth()->user()->settings->standalone_css === null)
    @vite('resources/sass/main.scss')

    @switch(auth()->user()->settings->style)
        @case(0)
            @vite('resources/sass/themes/_light.scss')

            @break
        @case(1)
            @vite('resources/sass/themes/_galactic.scss')

            @break
        @case(2)
            @vite('resources/sass/themes/_galactic.scss')
            @vite('resources/sass/themes/_dark-blue.scss')

            @break
        @case(3)
            @vite('resources/sass/themes/_galactic.scss')
            @vite('resources/sass/themes/_dark-green.scss')

            @break
        @case(4)
            @vite('resources/sass/themes/_galactic.scss')
            @vite('resources/sass/themes/_dark-pink.scss')

            @break
        @case(5)
            @vite('resources/sass/themes/_galactic.scss')
            @vite('resources/sass/themes/_dark-purple.scss')

            @break
        @case(6)
            @vite('resources/sass/themes/_galactic.scss')
            @vite('resources/sass/themes/_dark-red.scss')

            @break
        @case(7)
            @vite('resources/sass/themes/_galactic.scss')
            @vite('resources/sass/themes/_dark-teal.scss')

            @break
        @case(8)
            @vite('resources/sass/themes/_galactic.scss')
            @vite('resources/sass/themes/_dark-yellow.scss')

            @break
        @case(9)
            @vite('resources/sass/themes/_galactic.scss')
            @vite('resources/sass/themes/_cosmic-void.scss')

            @break
        @case(10)
            @vite('resources/sass/themes/_nord.scss')

            @break
        @case(11)
            @vite('resources/sass/themes/_revel.scss')

            @break
        @case(12)
            @vite('resources/sass/themes/_material-design-v3-light.scss')

            @break
        @case(13)
            @vite('resources/sass/themes/_material-design-v3-dark.scss')

            @break
        @case(14)
            @vite('resources/sass/themes/_material-design-v3-amoled.scss')

            @break
        @case(15)
            @vite('resources/sass/themes/_material-design-v3-navy.scss')

            @break
    @endswitch

    @if (isset(auth()->user()->settings->custom_css))
        <link rel="stylesheet" href="{{ auth()->user()->settings->custom_css }}" />
    @endif
@else
    <link rel="stylesheet" href="{{ auth()->user()->settings->standalone_css }}" />
@endif

@livewireStyles

@yield('stylesheets')
