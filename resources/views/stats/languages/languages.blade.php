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
    <li class="breadcrumb--active">
        {{ __('stat.languages') }}
    </li>
@endsection

@section('page', 'page__stats--languages')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.languages') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                @foreach ($languages as $code => $name)
                    <tr>
                        <td>{{ $name }}</td>
                        <td>
                            {{ __('member-interface.stats.language-used-by-count', ['count' => App\Models\UserSetting::where('locale', '=', $code)->count()]) }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </section>
@endsection
