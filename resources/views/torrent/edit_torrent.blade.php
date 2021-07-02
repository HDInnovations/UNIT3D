@extends('layout.default')

@section('breadcrumb')
    <li>
        <a href="{{ route('edit_form', ['id' => $torrent->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.torrent') @lang('common.edit')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="col-md-10">
            <h2>@lang('common.edit'): {{ $torrent->name }}</h2>
            <div class="block">
                <form role="form" method="POST" action="{{ route('edit', ['id' => $torrent->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title">@lang('torrent.title')</label>
                        <label>
                            <input type="text" class="form-control" name="name" value="{{ $torrent->name }}" required>
                        </label>
                    </div>
                    @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                    <div class="form-group">
                        <label for="name">TMDB ID <b>(@lang('common.required'))</b></label>
                        <label>
                            <input type="number" name="tmdb" value="{{ $torrent->tmdb }}" class="form-control" required>
                        </label>
                    </div>
                    @else
                        <input type="hidden" name="tmdb" value="0">
                    @endif

                    @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                    <div class="form-group">
                        <label for="name">IMDB ID <b>(@lang('torrent.optional'))</b></label>
                        <label>
                            <input type="number" name="imdb" value="{{ $torrent->imdb }}" class="form-control" required>
                        </label>
                    </div>
                    @else
                        <input type="hidden" name="imdb" value="0">
                    @endif

                    @if ($torrent->category->tv_meta)
                    <div class="form-group">
                        <label for="name">TVDB ID <b>(@lang('torrent.optional'))</b></label>
                        <label>
                            <input type="number" name="tvdb" value="{{ $torrent->tvdb }}" class="form-control" required>
                        </label>
                    </div>
                    @else
                        <input type="hidden" name="tvdb" value="0">
                    @endif

                    @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                    <div class="form-group">
                        <label for="name">MAL ID <b>(@lang('request.required') For Anime)</b></label>
                        <label>
                            <input type="number" name="mal" value="{{ $torrent->mal }}" class="form-control" required>
                        </label>
                    </div>
                    @else
                        <input type="hidden" name="mal" value="0">
                    @endif

                    @if ($torrent->category->game_meta)
                    <div class="form-group">
                        <label for="name">IGDB ID <b>@lang('request.required') For Games)</b></label>
                        <label>
                            <input type="number" name="igdb" value="{{ $torrent->igdb }}" class="form-control" required>
                        </label>
                    </div>
                    @else
                        <input type="hidden" name="igdb" value="0">
                    @endif
    
                    <div class="form-group">
                        <label for="category_id">@lang('torrent.category')</label>
                        <label>
                            <select name="category_id" class="form-control">
                                <option value="{{ $torrent->category->id }}" selected>{{ $torrent->category->name }}
                                    (@lang('torrent.current'))
                                </option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
    
                    <div class="form-group">
                        <label for="type">@lang('torrent.type')</label>
                        <label>
                            <select name="type_id" class="form-control">
                                <option value="{{ $torrent->type->id }}" selected>{{ $torrent->type->name }}
                                    (@lang('torrent.current'))
                                </option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    
                    @if ($torrent->category->no_meta)
                    <div class="form-group">
                        <label for="torrent-cover">Cover @lang('torrent.file') (@lang('torrent.optional'))</label>
                        <input class="upload-form-file" type="file" accept=".jpg, .jpeg" name="torrent-cover">
                    </div>
                    <div class="form-group">
                        <label for="torrent-banner">Banner @lang('torrent.file') (@lang('torrent.optional'))</label>
                        <input class="upload-form-file" type="file" accept=".jpg, .jpeg" name="torrent-banner">
                    </div>
                    @endif

                    @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                    <div class="form-group">
                        <label for="resolution_id">@lang('torrent.resolution')</label>
                        <label>
                            <select name="resolution_id" class="form-control">
                                @if (! $torrent->resolution)
                                    <option hidden="" disabled="disabled" selected="selected" value="">--Select Resolution--</option>)
                                @else
                                    <option value="{{ $torrent->resolution->id }}" selected>{{ $torrent->resolution->name }}
                                        (@lang('torrent.current'))
                                    </option>
                                @endif
                                @foreach ($resolutions as $resolution)
                                    <option value="{{ $resolution->id }}">{{ $resolution->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    @endif
    
                    <div class="form-group">
                        <label for="description">@lang('common.description')</label>
                        <label for="upload-form-description"></label>
                        <textarea id="upload-form-description" name="description" cols="30" rows="10" class="form-control">{{ $torrent->description }}</textarea>
                    </div>
    
                    <div class="form-group">
                        <label for="description">@lang('torrent.media-info')</label>
                        <label>
                            <textarea name="mediainfo" cols="30" rows="10" class="form-control">{{ $torrent->mediainfo }}</textarea>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="description">BDInfo (Quick Summary)</label>
                        <label>
                            <textarea name="bdinfo" cols="30" rows="10" class="form-control">{{ $torrent->bdinfo }}</textarea>
                        </label>
                    </div>
    
                    <label for="hidden" class="control-label">@lang('common.anonymous')?</label>
                    <div class="radio-inline">
                        <label><input type="radio" name="anonymous" @if ($torrent->anon == 1) checked
                            @endif value="1">@lang('common.yes')</label>
                    </div>
                    <div class="radio-inline">
                        <label><input type="radio" name="anonymous" @if ($torrent->anon == 0) checked
                            @endif value="0">@lang('common.no')</label>
                    </div>
                    <br>
                    <br>
                    <label for="hidden" class="control-label">@lang('torrent.stream-optimized')?</label>
                    <div class="radio-inline">
                        <label><input type="radio" name="stream" @if ($torrent->stream == 1) checked
                            @endif value="1">@lang('common.yes')</label>
                    </div>
                    <div class="radio-inline">
                        <label><input type="radio" name="stream" @if ($torrent->stream == 0) checked
                            @endif value="0">@lang('common.no')</label>
                    </div>
                    <br>
                    <br>
                    <label for="hidden" class="control-label">@lang('torrent.sd-content')?</label>
                    <div class="radio-inline">
                        <label><input type="radio" name="sd" @if ($torrent->sd == 1) checked
                            @endif value="1">@lang('common.yes')</label>
                    </div>
                    <div class="radio-inline">
                        <label><input type="radio" name="sd" @if ($torrent->sd == 0) checked
                            @endif value="0">@lang('common.no')</label>
                    </div>
                    <br>
                    <br>
                    @if (auth()->user()->hasPrivilegeTo('torrent_can_internal'))
                        <label for="internal" class="control-label">Internal?</label>
                        <div class="radio-inline">
                            <label><input type="radio" name="internal" @if ($torrent->internal == 1) checked
                                @endif value="1">@lang('common.yes')</label>
                        </div>
                        <div class="radio-inline">
                            <label><input type="radio" name="internal" @if ($torrent->internal == 0) checked
                                @endif value="0">@lang('common.no')</label>
                        </div>
                        <br>
                        <br>
                    @else
                        <input type="hidden" name="internal" value="0">
                    @endif
                    <button type="submit" class="btn btn-primary">@lang('common.submit')</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce('script') }}">
        $(document).ready(function() {
            $('#upload-form-description').wysibb({});
        })
    
    </script>
@endsection