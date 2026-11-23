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
        {{ __('request.requests') }}
    </li>
@endsection

@section('nav-tabs')
    @include('partials.statsrequestmenu')
@endsection

@section('page', 'page__stats--bountied')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.top-bountied') }}</h2>
        <div class="panelV2">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('request.request') }}</th>
                        <th>{{ __('request.bounty') }}</th>
                        <th>{{ __('request.filled') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bountied as $request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a
                                    href="{{ route('requests.show', ['torrentRequest' => $request]) }}"
                                >
                                    {{ $request->name }}
                                </a>
                            </td>
                            <td>{{ $request->bounty }}</td>
                            <td>
                                @if ($request->torrent_id === null)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times-circle text-danger"
                                        title="{{ __('stat.request-not-fulfilled') }}"
                                    ></i>
                                @elseif ($request->torrent_id !== null && $request->approved_when === null)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-question-circle text-info"
                                        title="{{ __('stat.request-pending-aproval') }}"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check-circle text-success"
                                        title="{{ __('stat.request-fulfilled') }}"
                                    ></i>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
