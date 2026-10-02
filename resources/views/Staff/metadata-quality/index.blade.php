@extends('layout.with-main')

@section('title')
    <title>
        {{ __('metadata-quality.index.title') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('metadata-quality.index.title') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('metadata-quality.index.title') }}</li>
@endsection

@section('page', 'page__staff-metadata-quality--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('metadata-quality.index.heading') }}</h2>
        </header>
        <form
            class="panel__body"
            method="GET"
            action="{{ route('staff.metadata-quality.index') }}"
        >
            <div class="form__group form__group--horizontal">
                <p class="form__group">
                    <select id="kind" name="kind" class="form__select">
                        <option value="">{{ __('metadata-quality.index.filter-kind-any') }}</option>
                        @foreach ($kinds as $kindOption)
                            <option value="{{ $kindOption }}" @selected($kind === $kindOption)>
                                {{ __('metadata-quality.kinds.'.$kindOption) }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="kind">
                        {{ __('metadata-quality.index.filter-kind') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="title"
                        name="title"
                        class="form__text"
                        type="text"
                        value="{{ $title }}"
                        placeholder="{{ __('metadata-quality.index.filter-title-placeholder') }}"
                    />
                    <label class="form__label form__label--floating" for="title">
                        {{ __('metadata-quality.index.filter-title') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="status" name="status" class="form__select">
                        @foreach (['all', 'missing_raw', 'missing_cover', 'missing_description', 'error'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected($status === $statusOption)>
                                {{ __('metadata-quality.status.'.$statusOption) }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="status">
                        {{ __('metadata-quality.index.filter-status') }}
                    </label>
                </p>
                <p class="form__group">
                    <button type="submit" class="form__button form__button--filled">
                        {{ __('metadata-quality.index.filter-submit') }}
                    </button>
                </p>
            </div>
        </form>
        @if ($errors->any())
            <div class="panel__body">
                @foreach ($errors->all() as $error)
                    <p class="text-red">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        @if (session('success'))
            <div class="panel__body">
                <p class="text-green">{{ session('success') }}</p>
            </div>
        @endif
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('metadata-quality.index.column-title') }}</th>
                        <th>{{ __('metadata-quality.index.column-kind') }}</th>
                        <th>{{ __('metadata-quality.index.column-source') }}</th>
                        <th>{{ __('metadata-quality.index.column-status') }}</th>
                        <th>{{ __('metadata-quality.index.column-updated') }}</th>
                        <th>{{ __('metadata-quality.index.column-actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($works as $work)
                        @php
                            $missingRaw = empty($work->raw);
                            $missingCover = empty($work->cover_url);
                            $missingDescription = $work->kind !== 'music' && trim((string) $work->description) === '';
                            $hasError = filled($work->metadata_error);
                        @endphp
                        <tr>
                            <td>{{ $work->title }}</td>
                            <td>{{ __('metadata-quality.kinds.'.$work->kind) }}</td>
                            <td>{{ $work->source }} #{{ $work->source_id }}</td>
                            <td>
                                @if ($hasError)
                                    <span class="text-red" title="{{ $work->metadata_error }}">
                                        <i class="{{ config('other.font-awesome') }} fa-exclamation-triangle"></i>
                                        {{ __('metadata-quality.status.error') }}
                                    </span>
                                @elseif ($missingRaw || $missingCover || $missingDescription)
                                    <span class="text-orange">
                                        <i class="{{ config('other.font-awesome') }} fa-exclamation-circle"></i>
                                        @if ($missingRaw)
                                            {{ __('metadata-quality.status.missing_raw') }}
                                        @elseif ($missingCover)
                                            {{ __('metadata-quality.status.missing_cover') }}
                                        @else
                                            {{ __('metadata-quality.status.missing_description') }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-green">
                                        <i class="{{ config('other.font-awesome') }} fa-check"></i>
                                        {{ __('metadata-quality.status.ok') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($work->metadata_updated_at)
                                    <time
                                        datetime="{{ $work->metadata_updated_at }}"
                                        title="{{ $work->metadata_updated_at }}"
                                    >
                                        {{ $work->metadata_updated_at->diffForHumans() }}
                                    </time>
                                @else
                                    {{ __('metadata-quality.index.never-refreshed') }}
                                @endif
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.metadata-quality.preview', ['work' => $work]) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            <button class="form__button form__button--text">
                                                {{ __('metadata-quality.index.action-preview') }}
                                            </button>
                                        </form>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ __('metadata-quality.index.no-works') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $works->links('partials.pagination') }}
    </section>
@endsection
