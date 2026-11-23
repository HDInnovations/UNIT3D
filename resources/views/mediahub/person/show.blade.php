@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.persons.index') }}" class="breadcrumb__link">
            {{ __('mediahub.persons') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $person->name }}
    </li>
@endsection

@section('page', 'page__person--show')

@section('main')
    @livewire('tmdb-person-credit', ['person' => $person])
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $person->name }}</h2>
        <img
            src="{{ isset($person->still) ? tmdb_image('cast_big', $person->still) : url('img/poster-placeholder.svg') }}"
            alt=""
            style="max-width: 100%"
        />
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('mediahub.born') }}</dt>
                <dd>{{ $person->birthday ?? __('common.unknown') }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Place of Birth</dt>
                <dd>{{ $person->place_of_birth ?? __('common.unknown') }}</dd>
            </div>
        </dl>
        <div class="panel__body">{{ $person->biography ?? 'No biography' }}</div>
    </section>
@endsection
