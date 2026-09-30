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

<link rel="icon" href="{{ url('/img/vltava-mark.svg') }}" type="image/svg+xml" />

@if (auth()->user()->settings->standalone_css === null)
    @php($theme = \App\Enums\Theme::fromStoredStyle(auth()->user()->settings->style))

    @vite('resources/sass/main.scss')

    @if ($theme->usesSystemPreference())
        <link
            rel="stylesheet"
            href="{{ Vite::asset('resources/sass/themes/_vltava-light.scss') }}"
            media="(prefers-color-scheme: light)"
        />
        <link
            rel="stylesheet"
            href="{{ Vite::asset('resources/sass/themes/_vltava-dark.scss') }}"
            media="(prefers-color-scheme: dark)"
        />
    @else
        @foreach ($theme->viteEntries() as $themeEntry)
            @vite($themeEntry)
        @endforeach
    @endif

    @if (isset(auth()->user()->settings->custom_css))
        <link rel="stylesheet" href="{{ auth()->user()->settings->custom_css }}" />
    @endif
@else
    <link rel="stylesheet" href="{{ auth()->user()->settings->standalone_css }}" />
@endif

@livewireStyles

@yield('stylesheets')
