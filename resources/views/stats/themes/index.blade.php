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
    <li class="breadcrumb--active">{{ __('member-interface.stats.themes') }}</li>
@endsection

@section('page', 'page__stats--themes')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('member-interface.stats.site-stylesheets') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                @forelse ($siteThemes as $siteTheme)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @switch($siteTheme->total_style)
                                @case('0')
                                    {{ __('member-interface.stats.theme-light') }}

                                    @break
                                @case('1')
                                    {{ __('member-interface.stats.theme-galactic') }}

                                    @break
                                @case('2')
                                    {{ __('member-interface.stats.theme-dark-blue') }}

                                    @break
                                @case('3')
                                    {{ __('member-interface.stats.theme-dark-green') }}

                                    @break
                                @case('4')
                                    {{ __('member-interface.stats.theme-dark-pink') }}

                                    @break
                                @case('5')
                                    {{ __('member-interface.stats.theme-dark-purple') }}

                                    @break
                                @case('6')
                                    {{ __('member-interface.stats.theme-dark-red') }}

                                    @break
                                @case('7')
                                    {{ __('member-interface.stats.theme-dark-teal') }}

                                    @break
                                @case('8')
                                    {{ __('member-interface.stats.theme-dark-yellow') }}

                                    @break
                                @case('9')
                                    {{ __('member-interface.stats.theme-cosmic-void') }}

                                    @break
                                @case('10')
                                    {{ __('member-interface.stats.theme-nord') }}

                                    @break
                                @case('11')
                                    {{ __('member-interface.stats.theme-revel') }}

                                    @break
                                @case('12')
                                    {{ __('member-interface.stats.theme-md3-light') }}

                                    @break
                                @case('13')
                                    {{ __('member-interface.stats.theme-md3-dark') }}

                                    @break
                                @case('14')
                                    {{ __('member-interface.stats.theme-md3-amoled') }}

                                    @break
                                @case('15')
                                    {{ __('member-interface.stats.theme-md3-navy') }}

                                    @break
                            @endswitch
                        </td>
                        <td>{{ __('member-interface.stats.theme-used-by-count', ['count' => $siteTheme->value]) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">{{ __('member-interface.stats.theme-none-used') }}</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </section>

    <section class="panelV2">
        <h2 class="panel__heading">{{ __('member-interface.stats.external-stylesheets') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                @forelse ($customThemes as $customTheme)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $customTheme->custom_css }}</td>
                        <td>{{ __('member-interface.stats.theme-used-by-count', ['count' => $customTheme->value]) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">{{ __('member-interface.stats.theme-none-used') }}</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </section>

    <section class="panelV2">
        <h2 class="panel__heading">{{ __('member-interface.stats.standalone-stylesheets') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                @forelse ($standaloneThemes as $standaloneTheme)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $standaloneTheme->standalone_css }}</td>
                        <td>{{ __('member-interface.stats.theme-used-by-count', ['count' => $standaloneTheme->value]) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">{{ __('member-interface.stats.theme-none-used') }}</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </section>
@endsection
