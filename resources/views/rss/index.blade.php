@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a class="breadcrumb__link" href="{{ route('torrents.index') }}">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('rss.rss') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('torrents.index') }}">
            {{ __('torrent.search') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('trending.index') }}">
            {{ __('common.trending') }}
        </a>
    </li>
    <li class="nav-tab--active">
        <a class="nav-tab--active__link" href="{{ route('rss.index') }}">
            {{ __('rss.rss') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('torrents.create') }}">
            {{ __('common.upload') }}
        </a>
    </li>
@endsection

@section('page', 'page__rss--index')

@section('main')
    <section class="panelV2" id="public">
        <h2 class="panel__heading">{{ __('rss.public') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.categories') }}</th>
                        <th>{{ __('common.types') }}</th>
                        <th>{{ __('common.resolutions') }}</th>
                        <th>{{ __('common.genres') }}</th>
                        <th>{{ __('torrent.discounts') }}</th>
                        <th>{{ __('common.special') }}</th>
                        <th>{{ __('torrent.health') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($public_rss as $rss)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('rss.show.rsskey', ['id' => $rss->id, 'rsskey' => auth()->user()->rsskey]) }}"
                                    target="_blank"
                                >
                                    {{ $rss->name }}
                                </a>
                            </td>
                            <td>
                                @if ($rss->object_torrent?->categories)
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
                                @if ($rss->object_torrent?->types)
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
                                @if ($rss->object_torrent?->resolutions)
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
                                @if ($rss->object_torrent?->genres)
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
                                @if ($rss->object_torrent?->freeleech || $rss->object_torrent->doubleupload || $rss->object_torrent->featured)
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
                                @if ($rss->object_torrent?->highspeed || $rss->object_torrent?->internal || $rss->object_torrent?->personalrelease || $rss->object_torrent?->bookmark)
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
                                @if ($rss->object_torrent?->alive || $rss->object_torrent?->dying || $rss->object_torrent?->dead)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No public RSS feeds</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="panelV2" id="private">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('rss.private') }}</h2>
            <div class="panel__actions">
                <a class="form__button form__button--text" href="{{ route('rss.create') }}">
                    {{ __('rss.create-private-feed') }}
                </a>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.categories') }}</th>
                        <th>{{ __('common.types') }}</th>
                        <th>{{ __('common.resolutions') }}</th>
                        <th>{{ __('common.genres') }}</th>
                        <th>{{ __('torrent.discounts') }}</th>
                        <th>{{ __('common.special') }}</th>
                        <th>{{ __('torrent.health') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($private_rss as $rss)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('rss.show.rsskey', ['id' => $rss->id, 'rsskey' => auth()->user()->rsskey]) }}"
                                    target="_blank"
                                >
                                    {{ $rss->name }}
                                </a>
                            </td>
                            <td>
                                @if ($rss->object_torrent?->categories)
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
                                @if ($rss->object_torrent?->types)
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
                                @if ($rss->object_torrent?->resolutions)
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
                                @if ($rss->object_torrent?->genres)
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
                                @if ($rss->object_torrent?->freeleech || $rss->object_torrent?->doubleupload || $rss->object_torrent?->featured)
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
                                @if ($rss->object_torrent?->highspeed || $rss->object_torrent?->internal || $rss->object_torrent?->personalrelease || $rss->object_torrent?->bookmark)
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
                                @if ($rss->object_torrent->alive || $rss->object_torrent->dying || $rss->object_torrent->dead)
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
                                @if (auth()->id() == $rss->user_id)
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                href="{{ route('rss.edit', ['id' => $rss->id]) }}"
                                                class="form__button form__button--text"
                                            >
                                                {{ __('common.edit') }}
                                            </a>
                                        </li>
                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('rss.destroy', ['id' => $rss->id]) }}"
                                                method="POST"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    x-on:click.prevent="confirmAction"
                                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this private RSS feed: ' . $rss->name . '?') }}"
                                                    class="form__button form__button--text"
                                                >
                                                    {{ __('common.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </menu>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No private RSS feeds</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
