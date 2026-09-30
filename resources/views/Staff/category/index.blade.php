@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.torrent-categories') }}
    </li>
@endsection

@section('page', 'page__staff-category--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('torrent.categories') }}</h2>
            <div class="panel__actions">
                <form class="panel__action" action="{{ route('staff.categories.create') }}">
                    <button class="form__button form__button--text">
                        {{ __('common.add') }}
                    </button>
                </form>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.position') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.icon') }}</th>
                        <th>{{ __('common.image') }}</th>
                        <th>{{ __('vltava.staff.movie_metadata') }}</th>
                        <th>{{ __('vltava.staff.tv_metadata') }}</th>
                        <th>{{ __('vltava.staff.game_metadata') }}</th>
                        <th>{{ __('vltava.staff.music_metadata') }}</th>
                        <th>{{ __('vltava.staff.no_metadata') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->position }}</td>
                            <td>
                                <a
                                    href="{{ route('staff.categories.edit', ['category' => $category]) }}"
                                >
                                    {{ $category->name }}
                                </a>
                            </td>
                            <td>
                                <i class="{{ $category->icon }}"></i>
                            </td>
                            <td>
                                @if ($category->image != null)
                                    <img
                                        alt=""
                                        src="{{ route('authenticated_images.category_image', ['category' => $category]) }}"
                                    />
                                @else
                                    <span>{{ __('vltava.staff.not_available') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($category->movie_meta)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($category->tv_meta)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($category->game_meta)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($category->music_meta)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($category->no_meta)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            class="form__button form__button--text"
                                            href="{{ route('staff.categories.edit', ['category' => $category]) }}"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.categories.destroy', ['category' => $category]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode(__('vltava.staff.delete_category', ['name' => $category->name])) }}"
                                                class="form__button form__button--text"
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
        </div>
    </section>
@endsection
