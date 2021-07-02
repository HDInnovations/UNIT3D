@extends('layout.default')

@section('title')
    <title>@lang('common.album') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="@lang('common.album')">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('albums.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('common.gallery')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('albums.create', ['id' => $album->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $album->name }} @lang('common.album')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            <div class="well">
                <img class="media-object pull-left" alt="{{ $album->name }}"
                    src="{{ url('files/img/' . $album->cover_image) }}" height="175px" width="auto"
                    style="margin-right: 20px;">
                <div class="media-body">
                    <h2 class="media-heading">@lang('common.album') @lang('common.name'):</h2>
                    <p class="text-bold">{{ $album->name }}</p>
                    <div class="media">
                        <h2 class="media-heading">@lang('common.album') @lang('common.description'):</h2>
                        <p class="text-bold">{{ $album->description }}</p>
                        @if (auth()->user()->hasRole('moderator') || (auth()->user()->id == $album->user_id &&
                            Carbon\Carbon::now()->lt($album->created_at->addDay())))
                            <form action="{{ route('albums.destroy', ['id' => $album->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a href="{{ route('images.create', ['id' => $album->id]) }}">
                                    <button type="button" class="btn btn-success btn-md">@lang('gallery.add-an-image-to') @lang('common.album')</button>
                                </a>
                                <button type="submit" class="btn btn-md btn-danger">@lang('common.delete')</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($album->images as $photo)
                    <div class="col-lg-3">
                        <div class="thumbnail" style="max-height: 450px; min-height: 400px;">
                            <img alt="{{ $album->name }}" src="{{ url('files/img/' . $photo->image) }}"
                                style="max-height: 300px; min-height: 300px; border: 6px solid rgb(128,128,128); border-radius: 5px;"
                                data-image='<img src="{{ url('files/img/' . $photo->image) }}" alt="Poster" style="height: 1000px;">'
                                class="show-image">
                            <div class="caption text-center">
                                <h4 class="label label-success">{{ $photo->type }}</h4>
                                <br>
                                <h4 class="badge badge-user"> @lang('gallery.uploaded-by'): {{ $photo->user->username }}</h4>
                                <br>
                                @if (auth()->user()->hasRole('moderator') || auth()->user()->id === $photo->user_id)
                                    <form action="{{ route('images.destroy', ['id' => $photo->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm">
                                            <i class="{{ config('other.font-awesome') }} fa-heart text-pink"></i>
                                        </button>
                                        <a href="{{ route('images.download', ['id' => $photo->id]) }}">
                                            <button type="button" class="btn btn-sm">
                                                <i class="{{ config('other.font-awesome') }} fa-download text-green">
                                                    {{ $photo->downloads }}
                                                </i>
                                            </button>
                                        </a>
                                        <button type="submit" class="btn btn-sm">
                                            <i class="{{ config('other.font-awesome') }} fa-times text-red"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce('script') }}">
        $('.show-image').click(function(e) {
            e.preventDefault();

          const name = $(this).attr('data-name')
          const image = $(this).attr('data-image')

          Swal.fire({
                showConfirmButton: false,
                showCloseButton: true,
                background: '#232323',
                width: 1200,
                html: image,
                title: name,
                text: '',
            });
        });
    
    </script>
@endsection
