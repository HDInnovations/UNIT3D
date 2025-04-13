<div class="panel__body playlists torrent__playlists">
    @forelse ($torrent->playlists as $playlist)
        <x-playlist.card :$playlist />
    @empty
        {{ __('playlist.about') }}
    @endforelse
</div>
