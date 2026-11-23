@props([
    'playlist',
])

<article class="playlists__playlist">
    @if (isset($playlist->cover_image) && Storage::disk('playlist-images')->exists($playlist->cover_image))
        <a
            class="playlists__playlist-image-link"
            href="{{ route('playlists.show', ['playlist' => $playlist]) }}"
        >
            <img
                class="playlists__playlist-image"
                src="{{ route('authenticated_images.playlist_image', ['playlist' => $playlist]) }}"
                alt=""
            />
        </a>
    @else
        <a
            class="playlists__playlist-image-link--none"
            href="{{ route('playlists.show', ['playlist' => $playlist]) }}"
        >
            <div class="playlists__playlist-image--none"></div>
        </a>
    @endif
    <div class="playlists__playlist-author">
        <x-user-tag :user="$playlist->user" :anon="false" />
    </div>
    <a
        class="playlists__playlist-link"
        href="{{ route('playlists.show', ['playlist' => $playlist]) }}"
    >
        <h3 class="playlists__playlist-name">
            {{ $playlist->name }}
        </h3>
    </a>
    <a
        class="playlists__playlist-link-titles"
        href="{{ route('playlists.show', ['playlist' => $playlist]) }}"
    >
        <p class="playlists__playlist-titles">
            {{ $playlist->torrents_count }} {{ __('playlist.titles') }}
        </p>
    </a>
</article>
