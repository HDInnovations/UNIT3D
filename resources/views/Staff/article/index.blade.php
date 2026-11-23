@extends('layout.with-main')

@section('title')
    <title>Articles - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.articles') }}
    </li>
@endsection

@section('page', 'page__staff-article--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('staff.articles') }}</h2>
            <div class="panel__actions">
                <a
                    class="panel__action form__button form__button--text"
                    href="{{ route('staff.articles.create') }}"
                >
                    {{ __('common.add') }}
                </a>
            </div>
        </header>
        <table class="data-table articles-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Date</th>
                    <th>{{ __('common.comments') }}</th>
                    <th>{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articles as $article)
                    <tr class="articles-table__article">
                        <td>
                            <a href="{{ route('staff.articles.edit', ['article' => $article]) }}">
                                {{ $article->title }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('users.show', ['user' => $article->user]) }}">
                                {{ $article->user->username }}
                            </a>
                        </td>
                        <td>
                            <time
                                datetime="{{ $article->created_at }}"
                                title="{{ $article->created_at }}"
                            >
                                {{ $article->created_at }}
                            </time>
                        </td>
                        <td>{{ $article->comments_count }}</td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    <a
                                        class="form__button form__button--text"
                                        href="{{ route('articles.show', ['article' => $article]) }}"
                                    >
                                        {{ __('common.view') }}
                                    </a>
                                </li>
                                <li class="data-table__action">
                                    <a
                                        class="form__button form__button--text"
                                        href="{{ route('staff.articles.edit', ['article' => $article]) }}"
                                    >
                                        {{ __('common.edit') }}
                                    </a>
                                </li>
                                <li class="data-table__action">
                                    <form
                                        action="{{ route('staff.articles.destroy', ['article' => $article]) }}"
                                        method="POST"
                                        x-data="confirmation"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            x-on:click.prevent="confirmAction"
                                            class="form__button form__button--text"
                                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this article: ' . $article->title . '?') }}"
                                        >
                                            {{ __('common.delete') }}
                                        </button>
                                    </form>
                                </li>
                            </menu>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $articles->links('partials.pagination') }}
    </section>
@endsection
