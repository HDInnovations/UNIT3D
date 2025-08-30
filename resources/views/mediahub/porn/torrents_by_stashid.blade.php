@extends('layout.with-main')

@section('title', 'Torrents for StashDB Scene ID: ' . $stashid)

@section('main')
    <h1>Torrents for StashDB Scene ID: <a href="{{ route('mediahub.porn.torrentsByStashId', $stashid) }}">{{ $stashid }}</a></h1>
    @php
        $fakeTorrent = (object) ['stashdb_id' => $stashid];
    @endphp
    @include('mediahub.porn.partials.porn-meta', ['torrent' => $fakeTorrent])
    @if ($torrents->count())
        <div class="panelV2 torrent-search__results">
            <header class="panel__header">
                <h2 class="panel__heading">Torrents</h2>
                <div class="panel__actions">
                    <div class="panel__action">
                        <span class="panel__action-text">
                            Total: {{ $torrents->count() }} |
                            Alive: {{ $torrents->where('seeders', '>', 0)->count() }} |
                            Dead: {{ $torrents->where('seeders', 0)->count() }}
                        </span>
                    </div>
                    <div class="panel__action">
                        <div class="form__group">
                            <form method="GET" style="display:inline-flex;gap:1em;align-items:center;">
                                <select id="view" name="view" class="form__select" onchange="this.form.submit()">
                                    <option value="list" {{ request('view', 'list') == 'list' ? 'selected' : '' }}>List</option>
                                    <option value="card" {{ request('view') == 'card' ? 'selected' : '' }}>Cards</option>
                                    <option value="group" {{ request('view') == 'group' ? 'selected' : '' }}>Groupings</option>
                                    <option value="poster" {{ request('view') == 'poster' ? 'selected' : '' }}>Poster</option>
                                </select>
                                <label class="fom__label form__label--floating" for="view">Layout</label>
                                <select id="perPage" name="perPage" class="form__select" onchange="this.form.submit()">
                                    <option value="25" {{ request('perPage', 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="75" {{ request('perPage') == 75 ? 'selected' : '' }}>75</option>
                                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <label class="form__label form__label--floating" for="perPage">Quantity</label>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            @php
                $view = request('view', 'list');
                $perPage = (int) request('perPage', 25);
                $displayTorrents = $torrents->take($perPage);
            @endphp
            @if ($view === 'list')
                <div class="data-table-wrapper torrent-search--list__results">
                    <table class="data-table">
                        <thead>
                            <tr
                                @class([
                                    'torrent-search--list__headers' => auth()->user()->settings->show_poster,
                                    'torrent-search--list__no-poster-headers' => !auth()->user()->settings->show_poster,
                                ])
                            >
                                @if(auth()->user()->settings->show_poster)
                                    <th class="torrent-search--list__poster-header">Poster</th>
                                @endif
                                <th class="torrent-search--list__format-header">Format</th>
                                <th class="torrent-search--list__name-header">Overview</th>
                                <th class="torrent-search--list__actions-header">Actions</th>
                                <th class="torrent-search--list__ratings-header">Rating</th>
                                <th class="torrent-search--list__size-header">Size</th>
                                <th class="torrent-search--list__seeders-header">Seeders</th>
                                <th class="torrent-search--list__leechers-header">Leechers</th>
                                <th class="torrent-search--list__completed-header">Completed</th>
                                <th class="torrent-search--list__age-header">Age</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($displayTorrents as $torrent)
                                @php
                                    $meta = $torrent->meta ?? null;
                                    $personalFreeleech = false;
                                @endphp
                                <x-torrent.row :torrent="$torrent" :meta="$meta" :personalFreeleech="$personalFreeleech" />
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="panel__body">Other layouts are not yet implemented.</div>
            @endif
        </div>
    @else
        <div class="panel__body">No torrents found for this stashid.</div>
    @endif
@endsection
