@extends('layout.with-main')

@section('title')
    <title>
        {{ __('metadata-quality.preview.title') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('metadata-quality.preview.title') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.metadata-quality.index') }}" class="breadcrumb__link">
            {{ __('metadata-quality.index.title') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('metadata-quality.preview.title') }}</li>
@endsection

@section('page', 'page__staff-metadata-quality--preview')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">
                {{ __('metadata-quality.preview.heading', ['title' => $work->title]) }}
            </h2>
        </header>
        <div class="panel__body">
            <p>{{ __('metadata-quality.preview.intro') }}</p>
        </div>
        <form
            class="panel__body"
            method="POST"
            action="{{ route('staff.metadata-quality.update', ['work' => $work]) }}"
            x-data="confirmation"
        >
            @csrf
            @method('PATCH')
            <input type="hidden" name="preview_token" value="{{ $token }}" />

            {{-- Title --}}
            <fieldset class="form__group">
                <legend>
                    <label>
                        <input
                            type="checkbox"
                            name="fields[]"
                            value="title"
                            @checked($diff['title']['changed'])
                        />
                        {{ __('metadata-quality.preview.select-field') }}: {{ __('metadata-quality.preview.field-title') }}
                    </label>
                </legend>
                <div class="form__group form__group--horizontal">
                    <p>
                        <strong>{{ __('metadata-quality.preview.current-value') }}:</strong>
                        {{ $diff['title']['old'] ?: __('metadata-quality.preview.no-value') }}
                    </p>
                    <p>
                        <strong>{{ __('metadata-quality.preview.new-value') }}:</strong>
                        {{ $diff['title']['new'] ?: __('metadata-quality.preview.no-value') }}
                        @unless ($diff['title']['changed'])
                            <em>({{ __('metadata-quality.preview.unchanged') }})</em>
                        @endunless
                    </p>
                </div>
            </fieldset>

            {{-- Description --}}
            <fieldset class="form__group">
                <legend>
                    <label>
                        <input
                            type="checkbox"
                            name="fields[]"
                            value="description"
                            @checked($diff['description']['changed'])
                        />
                        {{ __('metadata-quality.preview.select-field') }}: {{ __('metadata-quality.preview.field-description') }}
                    </label>
                </legend>
                @if ($diff['description']['expected_empty'] && trim((string) $diff['description']['new']) === '')
                    <p class="text-orange">{{ __('metadata-quality.preview.expected-empty-music') }}</p>
                @endif
                <div class="form__group form__group--horizontal">
                    <p>
                        <strong>{{ __('metadata-quality.preview.current-value') }}:</strong>
                        {{ $diff['description']['old'] ?: __('metadata-quality.preview.no-value') }}
                    </p>
                    <p>
                        <strong>{{ __('metadata-quality.preview.new-value') }}:</strong>
                        {{ $diff['description']['new'] ?: __('metadata-quality.preview.no-value') }}
                        @unless ($diff['description']['changed'])
                            <em>({{ __('metadata-quality.preview.unchanged') }})</em>
                        @endunless
                    </p>
                </div>
            </fieldset>

            {{-- Cover --}}
            <fieldset class="form__group">
                <legend>
                    <label>
                        <input
                            type="checkbox"
                            name="fields[]"
                            value="cover_url"
                            @checked($diff['cover_url']['changed'])
                        />
                        {{ __('metadata-quality.preview.select-field') }}: {{ __('metadata-quality.preview.field-cover_url') }}
                    </label>
                </legend>
                <div class="form__group form__group--horizontal">
                    <p>
                        <strong>{{ __('metadata-quality.preview.current-value') }}:</strong><br />
                        @if ($diff['cover_url']['old'])
                            <img
                                src="{{ $diff['cover_url']['old'] }}"
                                alt="{{ __('metadata-quality.preview.cover-preview-alt') }}"
                                loading="lazy"
                                width="120"
                            />
                        @else
                            {{ __('metadata-quality.preview.no-value') }}
                        @endif
                    </p>
                    <p>
                        <strong>{{ __('metadata-quality.preview.new-value') }}:</strong><br />
                        @if ($diff['cover_url']['new'])
                            <img
                                src="{{ $diff['cover_url']['new'] }}"
                                alt="{{ __('metadata-quality.preview.cover-preview-alt') }}"
                                loading="lazy"
                                width="120"
                            />
                        @else
                            {{ __('metadata-quality.preview.no-value') }}
                        @endif
                        @unless ($diff['cover_url']['changed'])
                            <br /><em>({{ __('metadata-quality.preview.unchanged') }})</em>
                        @endunless
                    </p>
                </div>
            </fieldset>

            {{-- Raw provider data --}}
            <fieldset class="form__group">
                <legend>
                    <label>
                        <input
                            type="checkbox"
                            name="fields[]"
                            value="raw"
                            @checked($diff['raw']['changed'])
                        />
                        {{ __('metadata-quality.preview.select-field') }}: {{ __('metadata-quality.preview.field-raw') }}
                    </label>
                </legend>
                <div class="form__group form__group--horizontal">
                    <div>
                        <strong>{{ __('metadata-quality.preview.current-value') }}:</strong>
                        <ul>
                            @forelse ($diff['raw']['old'] as $row)
                                <li>{{ $row['label'] }}: {{ $row['value'] }}</li>
                            @empty
                                <li>{{ __('metadata-quality.preview.no-value') }}</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <strong>{{ __('metadata-quality.preview.new-value') }}:</strong>
                        <ul>
                            @forelse ($diff['raw']['new'] as $row)
                                <li>{{ $row['label'] }}: {{ $row['value'] }}</li>
                            @empty
                                <li>{{ __('metadata-quality.preview.no-value') }}</li>
                            @endforelse
                        </ul>
                        @unless ($diff['raw']['changed'])
                            <em>({{ __('metadata-quality.preview.unchanged') }})</em>
                        @endunless
                    </div>
                </div>
            </fieldset>

            <p class="form__group form__group--horizontal">
                <a class="form__button form__button--text" href="{{ route('staff.metadata-quality.index') }}">
                    {{ __('metadata-quality.preview.back') }}
                </a>
                <button
                    type="submit"
                    x-on:click.prevent="confirmAction"
                    data-b64-deletion-message="{{ base64_encode(__('metadata-quality.preview.confirm-message')) }}"
                    class="form__button form__button--filled"
                >
                    {{ __('metadata-quality.preview.confirm') }}
                </button>
            </p>
        </form>
    </section>
@endsection
