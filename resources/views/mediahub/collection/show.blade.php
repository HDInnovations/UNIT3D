@extends('layout.default')

@section('title')
    <title>{{ $collection->name }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ $collection->name }}">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('mediahub.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('mediahub.title')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('mediahub.collections.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('mediahub.collections')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('mediahub.collections.show', ['id' => $collection->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $collection->name }}</span>
        </a>
    </li>
@endsection

@section('content')
<div class="torrent box container single">
    <div class="movie-wrapper">
        <div class="movie-overlay"></div>

        <div class="movie-poster">
            @php $tmdb_poster = $collection->poster ? \tmdb_image('poster_big', $collection->poster) : 'https://via.placeholder.com/400x600'; @endphp
            <img src="{{ $tmdb_poster }}" class="img-responsive" id="meta-poster">
        </div>

        <div class="meta-info">
            <div class="tags">
                @lang('mediahub.collections')
            </div>

            @php $tmdb_backdrop = $collection->backdrop ? \tmdb_image('back_big', $collection->backdrop) : 'https://via.placeholder.com/960x540'; @endphp
            <div class="movie-backdrop" style="background-image: url('{{ $tmdb_backdrop }}');"></div>

            <div class="movie-top">
                <h1 class="movie-heading">
                    <span class="text-bold">{{ $collection->name }}</span>
                </h1>

                <div class="movie-overview">
                    {{ $collection->overview }}
                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="panel panel-chat shoutbox">
        <div class="panel-heading">
            <h4><i class="{{ config("other.font-awesome") }} fa-film"></i> Movies</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-condensed table-bordered table-striped">
                <tbody>
                <tr>
                    <td>
                        <section class="recommendations">
                        @foreach($collection->movie as $movie)
                            <div class="item mini backdrop mini_card col-md-3">
                                <div class="image_content">
                                    <a href="{{ route('torrents.similar', ['category_id' => '1', 'tmdb' => $movie->id]) }}">
                                        <div>
                                            <img class="backdrop" src="{{ \tmdb_image('poster_mid', $movie->poster) }}">
                                        </div>
                                        <div style=" margin-top: 8px;">
                                            <span class="badge-extra"><i class="fas fa-calendar text-purple"></i> @lang('common.year'): {{ substr($movie->release_date, 0, 4) }}</span>
                                            <span class="badge-extra"><i class="fas fa-star text-gold"></i> @lang('torrent.rating'): {{ $movie->vote_average }}</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        </section>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="torrent box container" id="comments">
    <!-- Comments -->
    <div class="clearfix"></div>
    <div class="row ">
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-chat shoutbox">
                <div class="panel-heading">
                    <h4>
                        <i class="{{ config('other.font-awesome') }} fa-comment"></i> @lang('common.comments')
                    </h4>
                </div>
                <div class="panel-body no-padding">
                    <ul class="media-list comments-list">
                        @if (count($collection->comments) == 0)
                            <div class="text-center"><h4 class="text-bold text-danger"><i
                                            class="{{ config('other.font-awesome') }} fa-frown"></i> @lang('common.no-comments')!</h4>
                            </div>
                        @else
                            @foreach ($collection->comments as $comment)
                                <li class="media" style="border-left: 5px solid #01BC8C;">
                                    <div class="media-body">
                                        @if ($comment->anon == 1)
                                            <a href="#" class="pull-left" style="padding-right: 10px;">
                                                <img src="{{ url('img/profile.png') }}" class="img-avatar-48">
                                                <strong>{{ strtoupper(trans('common.anonymous')) }}</strong></a> @if (auth()->user()->id == $comment->user->id || auth()->user()->group->is_modo)
                                                <a href="{{ route('users.show', ['username' => $comment->user->username]) }}" style="color:{{ $comment->user->group->color }};">(<span><i class="{{ $comment->user->group->icon }}"></i> {{ $comment->user->username }}</span>)</a> @endif
                                        @else
                                            <a href="{{ route('users.show', ['username' => $comment->user->username]) }}"
                                               class="pull-left" style="padding-right: 10px;">
                                                @if ($comment->user->image != null)
                                                    <img src="{{ url('files/img/' . $comment->user->image) }}"
                                                         alt="{{ $comment->user->username }}" class="img-avatar-48"></a>
                                            @else
                                                <img src="{{ url('img/profile.png') }}"
                                                     alt="{{ $comment->user->username }}" class="img-avatar-48"></a>
                                            @endif
                                            <strong><a
                                                        href="{{ route('users.show', ['username' => $comment->user->username]) }}" style="color:{{ $comment->user->group->color }};"><span><i class="{{ $comment->user->group->icon }}"></i> {{ $comment->user->username }}</span></a></strong> @endif
                                        <span class="text-muted"><small><em>{{ $comment->created_at->toDayDateTimeString() }} ({{ $comment->created_at->diffForHumans() }})</em></small></span>
                                        @if ($comment->user_id == auth()->id() || auth()->user()->group->is_modo)
                                            <a title="@lang('common.delete-comment')"
                                               href="{{route('comment_delete',['comment_id'=>$comment->id])}}"><i
                                                        class="pull-right {{ config('other.font-awesome') }} fa fa-times" aria-hidden="true"></i></a>
                                            <a title="@lang('common.edit-comment')" data-toggle="modal"
                                               data-target="#modal-comment-edit-{{ $comment->id }}"><i
                                                        class="pull-right {{ config('other.font-awesome') }} fa-pencil"
                                                        aria-hidden="true"></i></a>
                                        @endif
                                        <div class="pt-5">
                                            @joypixels($comment->getContentHtml())
                                        </div>
                                    </div>
                                </li>
                                @include('partials.modals', ['comment' => $comment])
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Comments -->
        <br>
        <!-- Add comment -->
        <div class="col-md-12">
            <form role="form" method="POST" action="{{ route('comment_collection', ['id' => $collection->id]) }}">
                @csrf
                <div class="form-group">
                    <label for="content">@lang('common.your-comment'):</label>
                    <span class="badge-extra">BBCode @lang('common.is-allowed')</span>
                    <textarea id="content" name="content" cols="30" rows="5" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-danger">@lang('common.submit')</button>
                <label class="radio-inline"><strong>@lang('common.anonymous') @lang('common.comment')
                        :</strong></label>
                <input type="radio" value="1" name="anonymous"> @lang('common.yes')
                <input type="radio" value="0" checked="checked" name="anonymous"> @lang('common.no')
            </form>
        </div>
        <!-- /Add comment -->
    </div>
</div>
@endsection

@section('javascripts')
    <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce() }}">
      $(document).ready(function () {
        $('#content').wysibb({});
      })
    </script>
@endsection
