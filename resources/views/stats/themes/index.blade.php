@extends('layout.with-main')

@section('title')
    <title>{{ __('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('stats') }}" class="breadcrumb__link">
            {{ __('stat.stats') }}
        </a>
    </li>
    <li class="breadcrumb--active">Themes</li>
@endsection

@section('page', 'page__stats--themes')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Site stylesheets</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                @forelse ($siteThemes as $siteTheme)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @switch($siteTheme->total_style)
                                @case('0')
                                    Light theme

                                    @break
                                @case('1')
                                    Galactic theme

                                    @break
                                @case('2')
                                    Dark blue theme

                                    @break
                                @case('3')
                                    Dark green theme

                                    @break
                                @case('4')
                                    Dark pink theme

                                    @break
                                @case('5')
                                    Dark purple theme

                                    @break
                                @case('6')
                                    Dark red theme

                                    @break
                                @case('7')
                                    Dark teal theme

                                    @break
                                @case('8')
                                    Dark yellow theme

                                    @break
                                @case('9')
                                    Cosmic void theme

                                    @break
                                @case('10')
                                    Nord theme

                                    @break
                                @case('11')
                                    Revel theme

                                    @break
                                @case('12')
                                    Material design 3 light theme

                                    @break
                                @case('13')
                                    Material design 3 dark theme

                                    @break
                                @case('14')
                                    Material design 3 amoled theme

                                    @break
                                @case('15')
                                    Material design 3 navy theme

                                    @break
                            @endswitch
                        </td>
                        <td>Used by {{ $siteTheme->value }} users</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">None used</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </section>

    <section class="panelV2">
        <h2 class="panel__heading">External CSS stylesheets (stacks on top of above site theme)</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                @forelse ($customThemes as $customTheme)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $customTheme->custom_css }}</td>
                        <td>Used by {{ $customTheme->value }} users</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">None used</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </section>

    <section class="panelV2">
        <h2 class="panel__heading">Standalone CSS stylesheets (no site theme used)</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                @forelse ($standaloneThemes as $standaloneTheme)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $standaloneTheme->standalone_css }}</td>
                        <td>Used by {{ $standaloneTheme->value }} users</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">None used</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </section>
@endsection
