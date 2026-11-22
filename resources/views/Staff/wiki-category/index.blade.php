@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Wiki categories</li>
@endsection

@section('page', 'page__staff-wiki-category--index')

@section('main')
    @foreach ($wikiCategories as $category)
        <section class="panelV2">
            <header class="panel__header">
                <h2 class="panel__heading">{{ $category->name }}</h2>
                <div class="panel__actions">
                    <div class="panel__action">
                        <a
                            href="{{ route('staff.wikis.create', ['wikiCategoryId' => $category->id]) }}"
                            class="form__button form__button--text"
                        >
                            {{ __('common.add') }}
                        </a>
                    </div>
                    <div class="panel__action">
                        <a
                            href="{{ route('staff.wiki_categories.edit', ['wikiCategory' => $category]) }}"
                            class="form__button form__button--text"
                        >
                            {{ __('common.edit') }}
                        </a>
                    </div>
                    <form
                        action="{{ route('staff.wiki_categories.destroy', ['wikiCategory' => $category]) }}"
                        method="POST"
                        style="display: contents"
                        x-data="confirmation"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            class="form__button form__button--text"
                            x-on:click.prevent="confirmAction"
                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this wiki category (' . $category->name . ') and all wikis within?') }}"
                        >
                            {{ __('common.delete') }}
                        </button>
                    </form>
                </div>
            </header>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('common.created_at') }}</th>
                            <th>{{ __('torrent.updated_at') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($category->wikis as $wiki)
                            <tr>
                                <td>
                                    <a href="{{ route('wikis.show', ['wiki' => $wiki]) }}">
                                        {{ $wiki->name }}
                                    </a>
                                </td>
                                <td>{{ $wiki->created_at }}</td>
                                <td>{{ $wiki->updated_at }}</td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                href="{{ route('staff.wikis.edit', ['wiki' => $wiki]) }}"
                                                class="form__button form__button--text"
                                            >
                                                {{ __('common.edit') }}
                                            </a>
                                        </li>
                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('staff.wikis.destroy', ['wiki' => $wiki]) }}"
                                                method="POST"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    x-on:click.prevent="confirmAction"
                                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this wiki: ' . $wiki->name . '?') }}"
                                                    class="form__button form__button--text"
                                                >
                                                    {{ __('common.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </menu>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No wikis</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.actions') }}</h2>
        <div class="panel__body">
            <p class="form__group form__group--horizontal">
                <a
                    href="{{ route('staff.wiki_categories.create') }}"
                    class="form__button form__button--filled form__button--centered"
                >
                    Create new category
                </a>
            </p>
            <p class="form__group form__group--horizontal">
                <a
                    href="{{ route('staff.wikis.create') }}"
                    class="form__button form__button--filled form__button--centered"
                >
                    Create new wiki
                </a>
            </p>
        </div>
    </section>
@endsection
