<div class="movie-wrapper">
    <div class="movie-overlay"></div>
    <div class="movie-poster">
        @if(file_exists(public_path().'/files/img/torrent-cover_'.$torrent->id.'.jpg')) 
        <img src="{{ url('files/img/torrent-cover_' . $torrent->id . '.jpg') }}" class="img-responsive" id="meta-poster">
        @else
        <img src="https://via.placeholder.com/400x600" class="img-responsive" id="meta-poster">
        @endif
    </div>
    <div class="meta-info">
        <div class="tags">
            {{ $torrent->category->name }}
        </div>
        @if(file_exists(public_path().'/files/img/torrent-banner_'.$torrent->id.'.jpg')) 
        <div class="movie-backdrop" style="background-image: url({{ url('files/img/torrent-banner_' . $torrent->id . '.jpg') }});"></div>
        @else
        <div class="movie-backdrop" style="background-image: url('https://via.placeholder.com/960x540');"></div>
        @endif
    </div>
</div>