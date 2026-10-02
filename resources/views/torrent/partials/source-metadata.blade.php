@if ($metadata !== null)
    @php
        $metadataSourceAlias = strtolower(str_replace(' ', '-', $metadata->source));
        $metadataSourceLabel = match ($metadataSourceAlias) {
            'igdb' => 'IGDB',
            'tmdb' => 'TMDB',
            'musicbrainz' => 'MusicBrainz',
            'google-books' => 'Google Books',
            default => 'Open Library',
        };
    @endphp
    <section class="panelV2">
        <h2 class="panel__heading">
            <i class="{{ config('other.font-awesome') }} fa-database"></i>
            {{ __('media-interface.torrent.source-metadata-heading') }}
        </h2>
        <div class="panel__body">
            <dl class="torrent__meta">
                <dt>{{ __('common.title') }}</dt>
                <dd>{{ $metadata->title }}</dd>
                @if (!filled($metadata->raw))
                @if ($metadata->subtitle !== null)
                    <dt>{{ $metadataSourceAlias === 'musicbrainz' ? __('media-interface.torrent.source-metadata-performer') : __('common.author') }}</dt>
                    <dd>{{ $metadata->subtitle }}</dd>
                @endif
                @if ($metadata->released_on !== null)
                    <dt>{{ $metadataSourceAlias === 'musicbrainz' ? __('media-interface.torrent.source-metadata-release') : __('media-interface.torrent.source-metadata-published') }}</dt>
                    <dd>{{ $metadata->released_on }}</dd>
                @endif
                @if ($metadata->publisher !== null)
                    <dt>{{ __('media-interface.torrent.source-metadata-publisher') }}</dt>
                    <dd>{{ $metadata->publisher }}</dd>
                @endif
                @if ($metadata->item_count !== null)
                    <dt>{{ $metadataSourceAlias === 'musicbrainz' ? __('media-interface.torrent.source-metadata-tracks') : __('media-interface.torrent.source-metadata-pages') }}</dt>
                    <dd>{{ $metadata->item_count }}</dd>
                @endif
                @endif
            </dl>
            @if ($metadata->summary !== null)
                <p>{{ $metadata->summary }}</p>
            @endif
            @include('torrent.partials.rich-metadata', [
                'source' => $metadata->source,
                'raw' => $metadata->raw ?? null,
            ])
            <p>
                <a class="form__link" href="{{ $metadata->source_url }}" rel="noopener noreferrer" target="_blank">
                    {{ $metadataSourceLabel }}
                </a>
            </p>
        </div>
    </section>
@endif
