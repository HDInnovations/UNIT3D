@extends('layout.default')

@section('breadcrumb')
    <li>
        <a href="{{ route('playlists.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('playlist.playlists')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="block">
            <div class="header gradient yellow">
                <div class="inner_content">
                    <h1>@lang('playlist.playlists')</h1>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="container box text-center">
                <h2>@lang('playlist.playlists')</h2>
                <h4>@lang('playlist.about')</h4>
                <a href="{{ route('playlists.create') }}" class="btn btn-md btn-success">@lang('playlist.create')</a>
            </div>

            <div class="row">
                @foreach($playlists as $playlist)
                    <div class="col-md-2">
                        <a href="{{ route('playlists.show', ['id' => $playlist->id]) }}">
                            <div class="item-playlist-container-playlist">
                                @if(isset($playlist->cover_image))
                                    <img src="{{ url('files/img/' . $playlist->cover_image) }}" alt="Cover Image">
                                @else
                                    <div class="no_image_holder w273_and_h153 playlist"></div>
                                @endif
                                <div class="item-playlist-text-playlist">
                                    @if ($playlist->user->image != null)
                                        <img src="{{ url('files/img/' . $playlist->user->image) }}"
                                            alt="{{ $playlist->user->username }}">
                                    @else
                                        <img src="{{ url('img/profile.png') }}" alt="{{ $playlist->user->username }}">
                                    @endif
                                    <h3 class="text-bold" style="margin: 0; color: #cccccc;">{{ $playlist->name }}</h3>
                                    <h5>
                                        @lang('playlist.added-by') <b>{{ $playlist->user->username }}</b>
                                    </h5>
                                    <h6>{{ $playlist->torrents_count }} @lang('playlist.titles')</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="text-center">
                {{ $playlists->links() }}
            </div>
        </div>
    </div>
@endsection
