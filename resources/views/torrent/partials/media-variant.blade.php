@if ($variant !== null)
    <section class="panelV2">
        <h2 class="panel__heading"><i class="{{ config('other.font-awesome') }} fa-film"></i> {{ __('vltava.edition.title') }}</h2>
        <div class="panel__body">
            <p>
                <strong>{{ ucfirst(str_replace('_', ' ', $variant->edition->kind)) }}</strong>
                @if ($variant->edition->name !== null) · {{ $variant->edition->name }} @endif
                @if ($variant->edition->release_year !== null) · {{ $variant->edition->release_year }} @endif
            </p>
            <dl class="torrent__meta">
                <dt>{{ __('vltava.edition.source') }}</dt><dd>{{ $variant->source ?? __('common.unknown') }}</dd>
                <dt>{{ __('vltava.edition.video') }}</dt><dd>{{ collect([$variant->resolution, $variant->video_codec, $variant->hdr])->filter()->join(' · ') ?: __('common.unknown') }}</dd>
                <dt>{{ __('vltava.edition.bitrate') }}</dt><dd>{{ __('vltava.edition.video_rate', ['rate' => $variant->video_bit_rate ?? __('common.unknown')]) }} · {{ __('vltava.edition.overall_rate', ['rate' => $variant->overall_bit_rate ?? __('common.unknown')]) }}</dd>
                <dt>{{ __('vltava.edition.audio') }}</dt><dd>{{ collect($variant->audio_tracks)->map(fn (array $track) => collect([$track['language'] ?? null, $track['format'] ?? null, $track['channels'] ?? null, $track['bit_rate'] ?? null])->filter()->join(' '))->join(', ') ?: __('common.unknown') }}</dd>
                <dt>{{ __('vltava.edition.subtitles') }}</dt><dd>{{ collect($variant->subtitle_languages)->join(', ') ?: __('common.unknown') }}</dd>
            </dl>
            @if ($variant->edition->provenance !== null)<p>{{ $variant->edition->provenance }}</p>@endif
        </div>
    </section>
@endif
