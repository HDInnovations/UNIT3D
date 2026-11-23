@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('wikis.index') }}" class="breadcrumb__link">Wikis</a>
    </li>
    <li class="breadcrumb--active">
        {{ $wiki->name }}
    </li>
@endsection

@section('page', 'page__wiki--show')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $wiki->name }}</h2>
        <div class="panel__body bbcode-rendered">
            @joypixels($wiki->getContentHtml())
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('common.created_at') }}</dt>
                <dd>
                    <time datetime="{{ $wiki->created_at }}" title="{{ $wiki->created_at }}">
                        {{ $wiki->created_at }}
                    </time>
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.updated_at') }}</dt>
                <dd>
                    <time datetime="{{ $wiki->updated_at }}" title="{{ $wiki->updated_at }}">
                        {{ $wiki->updated_at }}
                    </time>
                </dd>
            </div>
        </dl>
    </section>
@endsection
