<section class="panelV2 panel--grid-item">
    <h2 class="panel__heading">{{ __('torrent.peers') }}</h2>
    <dl class="key-value">
        <div class="key-value__group">
            <dt>{{ __('torrent.seeders') }}</dt>
            <dd>{{ $num_seeders }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('torrent.leechers') }}</dt>
            <dd>{{ $num_leechers }}</dd>
        </div>
        <div class="key-value__group">
            <dt>Total</dt>
            <dd>{{ $num_peers }}</dd>
        </div>
        <div class="key-value__group">
            <dt>{{ __('torrent.seedsize') }}</dt>
            <dd>
                {{
                    \App\Helpers\StringHelper::formatBytes(
                        $totalSeeded,
                    )
                }}
            </dd>
        </div>
    </dl>
</section>
