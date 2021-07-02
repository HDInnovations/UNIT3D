@extends('layout.default')

@section('title')
    <title>Upload - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('torrents') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.torrents')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('upload_form', ['category_id' => $category_id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('common.upload')</span>
        </a>
    </li>
@endsection

@section('content')
    @if (! $user->hasPrivilegeTo('torrent_can_create'))
        <div class="container">
            <div class="jumbotron shadowed">
                <div class="container">
                    <h1 class="mt-5 text-center">
                        <i class="{{ config('other.font-awesome') }} fa-times text-danger"></i> @lang('torrent.cant-upload')!
                    </h1>
                    <div class="separator"></div>
                    <p class="text-center">@lang('torrent.cant-upload-desc')!</p>
                </div>
            </div>
        </div>
    @else
        <div class="torrent box container" id="preview-box" style="display:none">
            <h2 class="text-center mt-10">Upload Description Preview</h2>
            <div class="preview col-md-12" id="preview-content"></div>
        </div>
        <div class="torrent box container">
            <div class="alert alert-info text-center">
                <h2 class="mt-10"><strong>@lang('torrent.announce-url'):</strong>
                    {{ route('announce', ['passkey' => $user->passkey]) }}
                </h2>
                <p>@lang('torrent.announce-url-desc', ['source' => config('torrent.source')]).</p>
            </div>
            <br>
            <div class="text-center">
                <p class="text-success">{!! trans('torrent.announce-url-desc-url', ['url' => config('other.upload-guide_url')])
                    !!}
                </p>
                <p class="text-danger">@lang('torrent.announce-url-desc2')!</p>
            </div>

            <div class="upload col-md-12">
                <h2 class="upload-title">@lang('torrent.torrent')</h2>
                <form name="upload" class="upload-form" id="upload-form" method="POST" action="{{ route('upload') }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="torrent">Torrent @lang('torrent.file')</label>
                        <input class="upload-form-file" type="file" accept=".torrent" name="torrent" id="torrent" required>
                    </div>

                    <div class="form-group">
                        <label for="nfo">NFO @lang('torrent.file') (@lang('torrent.optional'))</label>
                        <input class="upload-form-file" type="file" accept=".nfo" name="nfo">
                    </div>
                    
                    @php $data = App\Models\Category::where('id', '=', !empty($category_id) ? $category_id : old('category_id'))->first();@endphp
                    @if ($data->no_meta)
                    <div class="form-group">
                        <label for="torrent-cover">Cover @lang('torrent.file') (@lang('torrent.optional'))</label>
                        <input class="upload-form-file" type="file" accept=".jpg, .jpeg" name="torrent-cover">
                    </div>
                    <div class="form-group">
                        <label for="torrent-banner">Banner @lang('torrent.file') (@lang('torrent.optional'))</label>
                        <input class="upload-form-file" type="file" accept=".jpg, .jpeg" name="torrent-banner">
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="name">@lang('torrent.title')</label>
                        <label for="title"></label>
                        <input type="text" name="name" id="title" class="form-control" value="{{ !empty($title) ? $title : old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">@lang('torrent.category')</label>
                        <label>
                            <select name="category_id" id="autocat" class="form-control" required>
                                <option hidden="" disabled="disabled" selected="selected" value="">--Select Category--</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if ($category_id==$category->id) selected="selected"@endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="type_id">@lang('torrent.type')</label>
                        <label>
                            <select name="type_id" id="autotype" class="form-control" required>
                                <option hidden="" disabled="disabled" selected="selected" value="">--Select Type--</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" @if (old('type_id')==$type->id) selected="selected"@endif>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    @if ($data->movie_meta || $data->tv_meta)
                    <div class="form-group">
                        <label for="resolution_ids">@lang('torrent.resolution')</label>
                        <label>
                            <select name="resolution_id" id="autores" class="form-control">
                                <option hidden="" disabled="disabled" selected="selected" value="">--Select Resolution--</option>
                                @foreach ($resolutions as $resolution)
                                    <option value="{{ $resolution->id }}" @if (old('resolution_id')==$resolution->id) selected="selected" @endif>
                                        {{ $resolution->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    @endif

                    @if ($data->tv_meta)
                        <div class="form-group">
                            <label for="season_number">@lang('torrent.season-number') <b>(@lang('request.required') For TV)</b></label>
                            <label>
                                <input type="number" name="season_number" id="season_number" class="form-control" value="{{ old('season_number') ?? '0' }}" required>
                            </label>
                        </div>
                    @endif

                    @if ($data->tv_meta)
                        <div class="form-group">
                            <label for="episode_number">@lang('torrent.episode-number') <b>(@lang('request.required') For TV. Use "0" For Season Packs.)</b></label>
                            <label>
                                <input type="number" name="episode_number" id="episode_number" class="form-control" value="{{ old('episode_number') ?? '0' }}" required>
                            </label>
                        </div>
                    @endif

                    @if ($data->movie_meta || $data->tv_meta)
                        <div class="form-group">
                            <label for="name">TMDB ID <b>(@lang('request.required'))</b></label>
                            <label>
                                <input type="text" name="apimatch" id="apimatch" class="form-control" value="" disabled>
                                <input type="number" name="tmdb" id="autotmdb" class="form-control" value="{{ !empty($tmdb) ? $tmdb : old('tmdb') }}" required>
                            </label>
                        </div>
                    @else
                        <input type="hidden" name="tmdb" value="0">
                    @endif

                    @if ($data->movie_meta || $data->tv_meta)
                        <div class="form-group">
                            <label for="name">IMDB ID <b>(@lang('torrent.optional'))</b></label>
                            <label>
                                @php $imdb_val = 0;
                                if (!empty($imdb)) {
                                    $imdb_val = $imdb;
                                }
                                if (!empty(old('imdb'))) {
                                    $imdb_val = old('imdb');
                                } @endphp
                                <input type="number" name="imdb" id="autoimdb" class="form-control" value="{{ $imdb_val }}">
                            </label>
                        </div>
                    @else
                        <input type="hidden" name="imdb" value="0">
                    @endif

                    @if ($data->tv_meta)
                        <div class="form-group">
                            <label for="name">TVDB ID (@lang('torrent.optional'))</label>
                            <label>
                                <input type="number" name="tvdb" id="autotvdb" value="{{ old('tvdb') ?? '0' }}" class="form-control" required>
                            </label>
                        </div>
                    @else
                        <input type="hidden" name="tvdb" value="0">
                    @endif

                    @if ($data->movie_meta || $data->tv_meta)
                        <div class="form-group">
                            <label for="name">MAL ID (@lang('torrent.required-anime'))</label>
                            <label>
                                <input type="number" name="mal" value="{{ old('mal') ?? '0' }}" class="form-control" required>
                            </label>
                        </div>
                    @else
                        <input type="hidden" name="mal" value="0">
                    @endif

                    @if ($data->game_meta)
                        <div class="form-group">
                            <label for="name">IGDB ID <b>(@lang('torrent.required-games'))</b></label>
                            <label>
                                <input type="number" name="igdb" value="{{ old('igdb') ?? '0' }}" class="form-control" required>
                            </label>
                        </div>
                    @else
                        <input type="hidden" name="igdb" value="0">
                    @endif

                    <div class="form-group">
                        <label for="name">@lang('torrent.keywords') (<i>@lang('torrent.keywords-example')</i>)</label>
                        <label>
                            <input type="text" name="keywords" id="autokeywords" class="form-control" value="{{ old('keywords') }}">
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="description">@lang('torrent.description')</label>
                        <label for="upload-form-description"></label>
                        <textarea id="upload-form-description" name="description" cols="30" rows="10" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    @if ($data->movie_meta || $data->tv_meta)
                        <div class="form-group">
                            <label for="mediainfo">@lang('torrent.media-info-parser')</label>
                            <label for="upload-form-description"></label>
                            <textarea id="upload-form-description" name="mediainfo" cols="30" rows="10" class="form-control" placeholder="@lang('torrent.media-info-paste')">{{ old('mediainfo') }}</textarea>
                        </div>
                    @endif

                    @if ($data->movie_meta || $data->tv_meta)
                        <div class="form-group">
                            <label for="bdinfo">BDInfo (Quick Summary)</label>
                            <label for="upload-form-description"></label>
                            <textarea id="upload-form-description" name="bdinfo" cols="30" rows="10" class="form-control" placeholder="Paste BDInfo Quick Summary">{{ old('bdinfo') }}</textarea>
                        </div>
                    @endif

                    <label for="anonymous" class="control-label">@lang('common.anonymous')?</label>
                    <div class="radio-inline">
                        <label><input type="radio" name="anonymous" value="1"{{ old('anonymous') ? ' checked' : '' }}>@lang('common.yes')</label>
                    </div>
                    <div class="radio-inline">
                        <label><input type="radio" name="anonymous" value="0"{{ !old('anonymous') ? ' checked' : '' }}>@lang('common.no')</label>
                    </div>

                    @if ($data->movie_meta || $data->tv_meta)
                        <br>

                        <label for="stream" class="control-label">@lang('torrent.stream-optimized')?</label>
                        <div class="radio-inline">
                            <label><input type="radio" name="stream" id="stream" value="1"{{ old('stream') ? ' checked' : '' }}>@lang('common.yes')</label>
                        </div>
                        <div class="radio-inline">
                            <label><input type="radio" name="stream" id="stream" value="0"{{ !old('stream') ? ' checked' : '' }}>@lang('common.no')</label>
                        </div>

                        <br>

                        <label for="sd" class="control-label">@lang('torrent.sd-content')?</label>
                        <div class="radio-inline">
                            <label><input type="radio" name="sd" value="1"{{ old('sd') ? ' checked' : '' }}>@lang('common.yes')</label>
                        </div>
                        <div class="radio-inline">
                            <label><input type="radio" name="sd" value="0"{{ !old('sd') ? ' checked' : '' }}>@lang('common.no')</label>
                        </div>
                    @else
                        <input type="hidden" name="stream" value="0">
                        <input type="hidden" name="sd" value="0">
                    @endif

                    <br>

                    @if (auth()->user()->hasPrivilegeTo('torrent_can_internal'))
                        <label for="internal" class="control-label">@lang('torrent.internal')?</label>
                        <div class="radio-inline">
                            <label><input type="radio" name="internal" value="1"{{ old('internal') ? ' checked' : '' }}>@lang('common.yes')</label>
                        </div>
                        <div class="radio-inline">
                            <label><input type="radio" name="internal" value="0"{{ !old('internal') ? ' checked' : '' }}>@lang('common.no')</label>
                        </div>

                        <br>
                    @else
                        <input type="hidden" name="internal" value="0">
                    @endif
                    
                    @if (auth()->user()->hasPrivilegeTo('torrent_can_freeleech'))
                        <label for="freeleech" class="control-label">@lang('torrent.freeleech')?</label>
                        <div class="radio-inline">
                            <label><input type="radio" name="free" value="1"{{ old('free') ? ' checked' : '' }}>@lang('common.yes')</label>
                        </div>
                        <div class="radio-inline">
                            <label><input type="radio" name="free" value="0"{{ !old('free') ? ' checked' : '' }}>@lang('common.no')</label>
                        </div>
                    @else
                        <input type="hidden" name="free" value="0">
                    @endif

                    <div class="text-center">
                        <button type="button" name="preview" value="true" id="preview" class="btn btn-info">
                            @lang('common.preview')
                        </button>
                        <button type="submit" name="post" value="true" id="post" class="btn btn-success">
                            @lang('common.submit')
                        </button>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    @endif
@endsection

@section('javascripts')
    <script src="{{ mix('js/imgbb.js') }}" crossorigin="anonymous"></script>
    <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce('script') }}">
        $('#preview').on('click', function() {
            var text = $('#upload-form-description').bbcode().trim();

            if (text.length > 0) {
                $.post('/upload/preview', {
                    'description': text
                }, function(data){
                    document.getElementById('preview-content').innerHTML = data;
                    document.getElementById('preview-box').removeAttribute('style');
                    document.getElementById('main-content').scrollIntoView({behavior: 'smooth'});
                });
            }
        });
    </script>
@endsection