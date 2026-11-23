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
    <li class="breadcrumbV2">
        <a href="{{ route('yearly_overviews.index') }}" class="breadcrumb__link">
            {{ __('common.overview') }}
        </a>
    </li>
@endsection

@section('page', 'page__yearly-overview--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Yearly overviews</h2>
        <div class="panel__body">
            <ul class="mediahub-card__list">
                @foreach ($siteYears as $siteYear)
                    <li class="mediahub-card__list-item">
                        <a
                            href="{{ route('yearly_overviews.show', ['year' => $siteYear]) }}"
                            class="mediahub-card"
                        >
                            <h2 class="mediahub-card__heading">{{ $siteYear }}</h2>
                            <h3 class="mediahub-card__subheading">Overview</h3>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection
