<div class="torrent__porn-meta">
    <h2>Porn Metadata</h2>
    @if (property_exists($torrent, 'fansdb_id') && $torrent->fansdb_id)
        <div><strong>FansDB ID:</strong> {{ $torrent->fansdb_id }}</div>
    @endif
    @if (property_exists($torrent, 'stashdb_id') && $torrent->stashdb_id)
        <div><strong>StashDB ID:</strong> <a href="{{ route('mediahub.porn.torrentsByStashId', $torrent->stashdb_id) }}">{{ $torrent->stashdb_id }}</a></div>
        @php
            $stashScene = (new \App\Services\StashDB\StashDBScraper())->scene($torrent->stashdb_id);
        @endphp
        @if ($stashScene)
            <div><strong>StashDB Scene Title:</strong> {{ $stashScene['title'] ?? 'N/A' }}</div>
            <div><strong>Release Date:</strong> {{ $stashScene['release_date'] ?? 'N/A' }}</div>
            <div><strong>Studio:</strong>
                {{ $stashScene['studio']['name'] ?? 'N/A' }}
            </div>
            <div><strong>Performers:</strong>
                @if (!empty($stashScene['performers']))
                    @foreach ($stashScene['performers'] as $performer)
                        @if (!empty($performer['performer']['id']) && !empty($performer['performer']['name']))
                            <a href="{{ route('mediahub.porn.performer', $performer['performer']['id']) }}">{{ $performer['performer']['name'] }}</a>@if (!$loop->last), @endif
                        @else
                            {{ $performer['performer']['name'] ?? 'N/A' }}@if (!$loop->last), @endif
                        @endif
                    @endforeach
                @else   
                    N/A
                @endif
            </div>
            <div><strong>Details:</strong> {{ $stashScene['details'] ?? 'N/A' }}</div>
            @if (!empty($stashScene['images']))
                <div><strong>Scene Images:</strong></div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    @foreach ($stashScene['images'] as $image)
                        <div>
                            <a href="{{ $image['url'] }}" target="_blank" style="display: flex; justify-content: center; align-items: center;">
                                <img src="{{ $image['url'] }}" alt="Scene Image" style="max-width: 1920px; max-height: 1080px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); display: block; margin: 0 auto;">
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    @endif
    @if (property_exists($torrent, 'theporndb_id') && $torrent->theporndb_id)
        <div><strong>ThePornDB ID:</strong> {{ $torrent->theporndb_id }}</div>
    @endif
</div>
