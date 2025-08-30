
@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.pornstars.index') }}" class="breadcrumb__link">
            {{ __('mediahub.pornstars') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $pornstar->name }}
    </li>
@endsection

@section('page', 'page__pornstar--show')

@section('main')
    <section class="panelV2">
        <h1 class="panel__heading">{{ $pornstar->name }}</h1>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>Aliases</dt>
                <dd>{{ is_array($pornstar->aliases) ? implode(', ', $pornstar->aliases) : $pornstar->aliases }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Age</dt>
                <dd>{{ $pornstar->age }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Gender</dt>
                <dd>{{ $pornstar->gender }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Country</dt>
                <dd>{{ $pornstar->country }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Ethnicity</dt>
                <dd>{{ $pornstar->ethnicity }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Eye Color</dt>
                <dd>{{ $pornstar->eye_color }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Hair Color</dt>
                <dd>{{ $pornstar->hair_color }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Cup Size</dt>
                <dd>{{ $pornstar->cup_size }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Band Size</dt>
                <dd>{{ $pornstar->band_size }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Waist Size</dt>
                <dd>{{ $pornstar->waist_size }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Hip Size</dt>
                <dd>{{ $pornstar->hip_size }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Breast Type</dt>
                <dd>{{ $pornstar->breast_type }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Birth Date</dt>
                <dd>{{ $pornstar->birth_date }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Height</dt>
                <dd>{{ $pornstar->height }} cm</dd>
            </div>
            <div class="key-value__group">
                <dt>Scene Count</dt>
                <dd>{{ $pornstar->scene_count }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Career Start</dt>
                <dd>{{ $pornstar->career_start_year }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Career End</dt>
                <dd>{{ $pornstar->career_end_year }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Links</dt>
                <dd>
                    @if (!empty($pornstar->urls))
                        @foreach ($pornstar->urls as $url)
                            <a href="{{ $url['url'] }}" target="_blank">{{ $url['type'] }}</a><br>
                        @endforeach
                    @endif
                </dd>
            </div>
        </dl>
    </section>
    <section class="panelV2" style="margin-top:2em;">
        <h2 class="panel__heading">Scenes Featuring {{ $pornstar->name }}</h2>
        @php
            // Collect unique stash_ids from torrents' scenes
            $stashIds = collect($pornstar->torrents ?? [])->map(function($torrent) {
                return $torrent->scene->id ?? null;
            })->filter()->unique();
        @endphp
        @if ($stashIds->count() > 0)
            <ul>
                @foreach ($stashIds as $stashid)
                    <li>
                        <a href="{{ route('mediahub.porn.gridByStashId', $stashid) }}">StashDB Scene #{{ $stashid }}</a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="panel__body">No scenes found for this pornstar.</div>
        @endif
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $pornstar->name }}</h2>
        @if (!empty($pornstar->images))
            @foreach ($pornstar->images as $img)
                <img src="{{ $img['url'] }}" alt="{{ $pornstar->name }}" style="max-width:160px;max-height:160px;border-radius:8px;margin-bottom:8px;">
            @endforeach
        @endif
    </section>

    <section class="panelV2" style="text-align:center;">
        <h2 class="panel__heading">Scenes Featuring {{ $pornstar->name }}</h2>
        @if (!empty($pornstar->torrents) && count($pornstar->torrents) > 0)
            @foreach ($pornstar->torrents as $torrent)
                @if (!empty($torrent->scene) && !empty($torrent->scene->images))
                    <img src="{{ $torrent->scene->images[0]['url'] ?? '' }}" alt="Scene Thumbnail" style="max-width:120px;max-height:120px;border-radius:8px;margin:8px auto;display:block;">
                @endif
            @endforeach
        @else
            <div class="panel__body">No scenes found for this pornstar.</div>
        @endif
    </section>
@endsection
