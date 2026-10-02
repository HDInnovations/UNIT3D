@extends('layout.with-main')

@section('title')
    <title>{{ $work->title }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.index') }}" class="breadcrumb__link">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $work->title }}
    </li>
@endsection

@section('page', 'page__work-show')

@section('main')
    @php
        $year = $work->displayYear();

        $facets = $work->facets ?? [];

        // MediaWorkController::show doesn't pass this (it's not part of its
        // View contract); mirrors the same cache lookup TorrentSearch uses
        // so the shared _torrent-icons partial (via _media-work-variant-row)
        // still resolves personal-freeleech status correctly here.
        $personalFreeleech = cache()->get('personal_freeleech:'.auth()->id()) ?? false;
    @endphp

    <section class="panelV2 work__panel">
        <header class="panel__header work__header">
            <img
                src="{{ $work->cover_url ?? asset('img/no-poster.png') }}"
                alt="{{ $work->title }}"
                loading="lazy"
                class="work__cover"
            />
            <div class="work__summary">
                <h1 class="work__title">
                    {{ $work->title }}
                    @if ($year !== null)
                        <span class="work__year">({{ $year }})</span>
                    @endif
                </h1>

                <p class="work__description">
                    {{ $work->description ?: __('catalog.work.no-description') }}
                </p>

                {{--
                    Only Work-common facets are shown here (see
                    App\Services\Media\MediaWorkCatalog::computeFacets):
                    format/language/publisher legitimately vary per edition
                    and stay on each variant's own torrent page, never
                    labelled as universal to the whole Work.
                --}}
                @if (!empty($facets))
                    <dl class="work__facets">
                        @if ($work->kind === 'music')
                            @if (!empty($facets['artists']))
                                <div class="work__facet">
                                    <dt>{{ __('catalog.work.artists') }}</dt>
                                    <dd>{{ implode(', ', $facets['artists']) }}</dd>
                                </div>
                            @endif
                            
                        @elseif ($work->kind === 'game')
                            @if (!empty($facets['platforms']))
                                <div class="work__facet">
                                    <dt>{{ __('catalog.work.platforms') }}</dt>
                                    <dd>{{ implode(', ', $facets['platforms']) }}</dd>
                                </div>
                            @endif
                            @if (!empty($facets['genres']))
                                <div class="work__facet">
                                    <dt>{{ __('catalog.work.genres') }}</dt>
                                    <dd>{{ implode(', ', $facets['genres']) }}</dd>
                                </div>
                            @endif
                            @if (!empty($facets['developers']))
                                <div class="work__facet">
                                    <dt>{{ __('catalog.work.developers') }}</dt>
                                    <dd>{{ implode(', ', $facets['developers']) }}</dd>
                                </div>
                            @endif
                        @elseif ($work->kind === 'book')
                            @if (!empty($facets['authors']))
                                <div class="work__facet">
                                    <dt>{{ __('catalog.work.authors') }}</dt>
                                    <dd>{{ implode(', ', $facets['authors']) }}</dd>
                                </div>
                            @endif
                        @endif
                    </dl>
                @endif

                @if ($work->source_url)
                    <a
                        class="work__source-link"
                        href="{{ $work->source_url }}"
                        target="_blank"
                        rel="noreferrer"
                    >
                        {{ __('catalog.work.source') }}
                    </a>
                @endif
            </div>
        </header>
    </section>

    @if (filled($work->raw))
        <section class="panelV2 work__metadata">
            <h2 class="panel__heading">{{ __('media-interface.torrent.source-metadata-heading') }}</h2>
            <div class="panel__body">
                @php
                    $commonDetails = \App\Services\Metadata\MetadataDetails::forSource((string) $work->source, $work->raw);
                    if (in_array($work->kind, ['music', 'book'], true)) {
                        $commonKeys = $work->kind === 'music'
                            ? ['genres', 'tags', 'primary_type', 'secondary_types']
                            : ['subjects', 'classifications', 'dewey', 'lc_classifications'];
                        $commonDetails = array_values(array_filter($commonDetails, fn ($row) => in_array($row['key'], $commonKeys, true)));
                    }
                @endphp
                @if (in_array($work->kind, ['music', 'book'], true))
                    <p class="form__hint">{{ __('catalog.work.edition-source-hint') }}</p>
                @endif
                @include('torrent.partials.rich-metadata', ['source' => $work->source, 'raw' => $work->raw, 'details' => $commonDetails])
            </div>
        </section>
    @endif

    <section class="panelV2 work__variants">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('catalog.work.variants') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <span class="panel__action-text">
                        {{ __('catalog.work.variants-count', ['count' => $variants->total()]) }}
                    </span>
                </div>
            </div>
        </header>

        {{ $variants->links('partials.pagination') }}

        @if ($variants->isEmpty())
            <p>{{ __('catalog.work.no-variants') }}</p>
        @else
            @php
                // Nests this page's variants as type -> torrents (and, for
                // tv, under their season/episode/package content scope; see
                // CONTEXT.md: a different scope is different content, not
                // merely different quality), matching the grouping already
                // used by the search results' media-work card.
                $scopeGroups = [];

                foreach ($variants as $torrent) {
                    $scopeLabel = '';

                    if ($work->kind === 'tv') {
                        $season = (int) $torrent->season_number;
                        $episode = (int) $torrent->episode_number;

                        $scopeLabel = match (true) {
                            $season === 0 && $episode === 0 => __('livewire-interface.complete-pack'),
                            $season === 0 => __('catalog.scope.special', ['episode' => $episode]),
                            $episode === 0 => __('catalog.scope.season', ['season' => $season]),
                            default => __('catalog.scope.season-episode', ['season' => $season, 'episode' => $episode]),
                        };
                    }

                    $typeName = (string) ($torrent->type?->name);
                    $scopeGroups[$scopeLabel][$typeName][] = $torrent;
                }
            @endphp
            <table class="data-table torrent-search--grouped__torrents work__variant-table">
                <colgroup>
                    <col class="work__variant-scope" />
                    <col />
                    <col class="work__variant-action" span="3" />
                    <col class="work__variant-size" />
                    <col class="work__variant-peers" span="2" />
                    <col class="work__variant-completed" />
                    <col class="work__variant-age" />
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">{{ __('catalog.work.column-scope') }} / {{ __('catalog.work.column-type') }}</th>
                        <th scope="col">{{ __('catalog.work.column-release') }}</th>
                        <th scope="colgroup" colspan="3">{{ __('common.actions') }}</th>
                        <th scope="col">{{ __('torrent.size') }}</th>
                        <th scope="col">{{ __('torrent.seeders') }}</th>
                        <th scope="col">{{ __('torrent.leechers') }}</th>
                        <th scope="col">{{ __('torrent.completed') }}</th>
                        <th scope="col">{{ __('common.created_at') }}</th>
                    </tr>
                </thead>
                @foreach ($scopeGroups as $scopeLabel => $typeGroups)
                    @foreach ($typeGroups as $typeName => $torrents)
                        <tbody>
                            @foreach ($torrents as $torrent)
                                <tr>
                                    @if ($loop->first)
                                        <th
                                            class="torrent-search--grouped__type"
                                            scope="rowgroup"
                                            rowspan="{{ $loop->count }}"
                                        >
                                            {{ $scopeLabel !== '' ? "{$scopeLabel} — {$typeName}" : $typeName }}
                                        </th>
                                    @endif

                                    @include('components.partials._media-work-variant-row')
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
                @endforeach
            </table>
        @endif

        {{ $variants->links('partials.pagination') }}
    </section>
@endsection
