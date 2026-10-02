@php
    use App\Services\Metadata\MetadataDetails;

    $rawPayload = $raw ?? null;
    $sourceName = $source ?? null;
    $detailsRows = $details ?? null;

    $rawArray = is_array($rawPayload) ? $rawPayload : null;

    $detailsRows = is_array($detailsRows)
        ? $detailsRows
        : ($rawArray !== null && is_string($sourceName) ? MetadataDetails::forSource($sourceName, $rawArray) : []);
    $rawJson = $rawArray === null
        ? null
        : json_encode($rawArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    $rawJson = is_string($rawJson) ? $rawJson : null;
@endphp

@if ($detailsRows !== [] || $rawJson !== null)
    <div class="rich-metadata">
        @if ($detailsRows !== [])
            <h3 class="rich-metadata__heading">{{ __('metadata.sections.summary') }}</h3>
            <dl class="torrent__meta rich-metadata__list">
                @foreach ($detailsRows as $row)
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                @endforeach
            </dl>
        @endif

        @if ($rawJson !== null)
            <details class="rich-metadata__raw">
                <summary class="rich-metadata__summary">
                    {{ __('metadata.sections.advanced') }}
                </summary>
                <pre class="rich-metadata__json"><code>{{ $rawJson }}</code></pre>
            </details>
        @endif
    </div>
@endif
