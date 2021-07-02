@extends('layout.default')

@section('title')
    <title>@lang('rss.edit-private-feed') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('rss.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('rss.rss')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('rss.edit', ['id' => $rss->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('rss.edit')</span>
        </a>
    </li>
@endsection


@section('content')
    <div class="container">
        <h1 class="upload-title">@lang('rss.edit-private-feed')</h1>
        <form role="form" method="POST" action="{{ route('rss.update', ['id' => $rss->id]) }}">
            @csrf
            @method('PATCH')
            <div class="block">
                <div class="upload col-md-12">
                    <div class="form-group">
                        <label for="name">@lang('rss.feed') @lang('rss.name')</label>
                        <div>{{ $rss->name }}</div>
                    </div>
                    <div class="form-group">
                        <label for="search">@lang('torrent.torrent') @lang('torrent.name')</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ $rss->object_torrent->search }}">
                    </div>
                    <div class="form-group">
                        <label for="description">@lang('torrent.torrent') @lang('torrent.description')</label>
                        <input type="text" class="form-control" id="description" name="description"
                            value="{{ $rss->object_torrent->description }}">
                    </div>
                    <div class="form-group">
                        <label for="uploader">@lang('torrent.torrent') @lang('torrent.uploader')</label>
                        <input type="text" class="form-control" id="uploader" name="uploader"
                            value="{{ $rss->object_torrent->uploader }}">
                    </div>

                    <div class="form-group">
                        <label for="imdb">IMDB ID</label>
                        <input type="text" class="form-control" id="imdb" name="imdb"
                            value="{{ $rss->object_torrent->imdb }}">
                        <label for="tvdb">TVDB ID</label>
                        <input type="text" class="form-control" id="tvdb" name="tvdb"
                            value="{{ $rss->object_torrent->tvdb }}">
                        <label for="tmdb">TMDB ID</label>
                        <input type="text" class="form-control" id="tmdb" name="tmdb"
                            value="{{ $rss->object_torrent->tmdb }}">
                        <label for="mal">MAL ID</label>
                        <input type="text" class="form-control" id="mal" name="mal" value="{{ $rss->object_torrent->mal }}">
                    </div>

                    <div class="form-group">
                        <label for="category">@lang('torrent.category')</label>
                        <div>
                            @foreach ($torrent_repository->categories() as $id => $category)
                                <span class="badge-user">
                                    <label class="inline">
                                        @if(is_array($rss->object_torrent->categories) &&
                                            in_array((string)$id, $rss->object_torrent->categories, true))
                                            <input type="checkbox" id="{{ $category }}" name="categories[]" value="{{ $id }}"
                                                class="category" CHECKED> {{ $category }}
                                        @else
                                            <input type="checkbox" id="{{ $category }}" name="categories[]" value="{{ $id }}"
                                                class="category"> {{ $category }}
                                        @endif
                                    </label>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('torrent.type')</label>
                        <div>
                            @foreach ($torrent_repository->types() as $id => $type)
                                <span class="badge-user">
                                    <label class="inline">
                                        @if(is_array($rss->object_torrent->types) &&
                                            in_array((string)$id, $rss->object_torrent->types, true))
                                            <input type="checkbox" id="{{ $type }}" name="types[]" value="{{ $id }}" class="type"
                                                CHECKED> {{ $type }}
                                        @else
                                            <input type="checkbox" id="{{ $type }}" name="types[]" value="{{ $id }}" class="type">
                                            {{ $type }}
                                        @endif
                                    </label>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="resolution">@lang('torrent.resolution')</label>
                        <div>
                            @foreach ($torrent_repository->resolutions() as $id => $resolution)
                                <span class="badge-user">
                            <label class="inline">
                                @if(is_array($rss->object_torrent->resolutions) && in_array((string)$id, $rss->object_torrent->resolutions, true))
                                    <input type="checkbox" id="{{ $resolution }}" name="resolutions[]" value="{{ $id }}" class="resolution" CHECKED>
                                    {{ $resolution }}
                                @else
                                    <input type="checkbox" id="{{ $resolution }}" name="resolutions[]" value="{{ $id }}" class="resolution">
                                    {{ $resolution }}
                                @endif
                            </label>
                        </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="genre">@lang('torrent.genre')</label>
                        <div>
                            @foreach ($torrent_repository->genres() as $id => $genre)
                                <span class="badge-user">
                                    <label class="inline">
                                        @if(is_array($rss->object_torrent->genres) &&
                                            in_array($genre, $rss->object_torrent->genres, true))
                                            <input type="checkbox" id="{{ $genre }}" name="genres[]" value="{{ $genre }}"
                                                class="genre" CHECKED> {{ $genre }}
                                        @else
                                            <input type="checkbox" id="{{ $genre }}" name="genres[]" value="{{ $genre }}"
                                                class="genre"> {{ $genre }}
                                        @endif
                                    </label>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('torrent.discounts')</label>
                        <div>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->freeleech)
                                        <input type="checkbox" id="freeleech" name="freeleech" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-star text-gold"></span>
                                        @lang('torrent.freeleech')
                                    @else
                                        <input type="checkbox" id="freeleech" name="freeleech" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-star text-gold"></span>
                                        @lang('torrent.freeleech')
                                    @endif
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->doubleupload)
                                        <input type="checkbox" id="doubleupload" name="doubleupload" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-gem text-green"></span>
                                        @lang('torrent.double-upload')
                                    @else
                                        <input type="checkbox" id="doubleupload" name="doubleupload" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-gem text-green"></span>
                                        @lang('torrent.double-upload')
                                    @endif
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->featured)
                                        <input type="checkbox" id="featured" name="featured" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-certificate text-pink"></span>
                                        @lang('torrent.featured')
                                    @else
                                        <input type="checkbox" id="featured" name="featured" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-certificate text-pink"></span>
                                        @lang('torrent.featured')
                                    @endif
                                </label>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('torrent.special')</label>
                        <div>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->stream)
                                        <input type="checkbox" id="stream" name="stream" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-play text-red"></span>
                                        @lang('torrent.stream-optimized')
                                    @else
                                        <input type="checkbox" id="stream" name="stream" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-play text-red"></span>
                                        @lang('torrent.stream-optimized')
                                    @endif
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->highspeed)
                                        <input type="checkbox" id="highspeed" name="highspeed" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-tachometer text-red"></span>
                                        @lang('common.high-speeds')
                                    @else
                                        <input type="checkbox" id="highspeed" name="highspeed" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-tachometer text-red"></span>
                                        @lang('common.high-speeds')
                                    @endif
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->sd)
                                        <input type="checkbox" id="sd" name="sd" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-ticket text-orange"></span>
                                        @lang('torrent.sd-content')
                                    @else
                                        <input type="checkbox" id="sd" name="sd" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-ticket text-orange"></span>
                                        @lang('torrent.sd-content')
                                    @endif
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->internal)
                                        <input type="checkbox" id="internal" name="internal" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-magic" style="color: #baaf92;"></span>
                                        @lang('torrent.internal')
                                    @else
                                        <input type="checkbox" id="internal" name="internal" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-magic" style="color: #baaf92;"></span>
                                        @lang('torrent.internal')
                                    @endif
                                </label>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type">@lang('torrent.health')</label>
                        <div>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->alive)
                                        <input type="checkbox" id="alive" name="alive" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-smile text-green"></span>
                                        @lang('torrent.alive')
                                    @else
                                        <input type="checkbox" id="alive" name="alive" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-smile text-green"></span>
                                        @lang('torrent.alive')
                                    @endif
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->dying)
                                        <input type="checkbox" id="dying" name="dying" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-meh text-orange"></span>
                                        @lang('torrent.dying-torrent')
                                    @else
                                        <input type="checkbox" id="dying" name="dying" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-meh text-orange"></span>
                                        @lang('torrent.dying-torrent')
                                    @endif
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    @if($rss->object_torrent->dead)
                                        <input type="checkbox" id="dead" name="dead" value="1" CHECKED><span
                                            class="{{ config('other.font-awesome') }} fa-frown text-red"></span>
                                        @lang('torrent.dead-torrent')
                                    @else
                                        <input type="checkbox" id="dead" name="dead" value="1"><span
                                            class="{{ config('other.font-awesome') }} fa-frown text-red"></span>
                                        @lang('torrent.dead-torrent')
                                    @endif
                                </label>
                            </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">@lang('common.edit')</button>
                </div>
            </div>
        </form>
    </div>
@endsection
