@extends('layout.default')

@section('title')
    <title>{{ $article->title }} - @lang('articles.articles') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ substr(strip_tags($article->content), 0, 200) }}...">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('articles.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('articles.articles')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('articles.show', ['id' => $article->id]) }}" itemprop="url"
           class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $article->title }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="box container">
        <a href="{{ route('articles.show', ['id' => $article->id]) }}"
           style=" float: right; margin-right: 10px;">
            @if ( ! is_null($article->image))
                <img src="{{ url('files/img/' . $article->image) }}" alt="{{ $article->title }}">
            @else
                <img src="{{ url('img/missing-image.png') }}" alt="{{ $article->title }}">
            @endif
        </a>

        <h1 class="text-bold" style="display: inline ;">{{ $article->title }}</h1>

        <p class="text-muted">
            <em>@lang('articles.published-at') {{ $article->created_at->toDayDateTimeString() }}</em>
        </p>

        <p style="margin-top: 20px;">@joypixels($article->getContentHtml())</p>
    </div>

    <div class="box container">
        <div class="clearfix"></div>
        <div class="row ">
            <div class="col-md-12">
                <div class="panel panel-danger">
                    <div class="panel-heading border-light">
                        <h4 class="panel-title">
                            <i class="{{ config('other.font-awesome') }} fa-comment"></i> @lang('common.comments')
                        </h4>
                    </div>
                    <div class="panel-body no-padding">
                        <ul class="media-list comments-list">
                            @if (count($article->comments) == 0)
                                <div class="text-center"><h4 class="text-bold text-danger"><i
                                                class="{{ config('other.font-awesome') }} fa-frown"></i> @lang('common.no-comments')!</h4>
                                </div>
                            @else
                                @foreach ($article->comments as $comment)
                                    <li class="media" style="border-left: 5px solid #01bc8c;">
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
                                            <span class="text-muted"><small><em>{{$comment->created_at->diffForHumans() }}</em></small></span>
                                            @if ($comment->user_id == auth()->id() || auth()->user()->group->is_modo)
                                                <a title="@lang('common.delete-your-comment')"
                                                   href="{{route('comment_delete',['comment_id'=>$comment->id])}}"><i
                                                            class="pull-right btn btn-danger btn-xs {{ config('other.font-awesome') }} fa fa-times" aria-hidden="true"></i></a>
                                                <a title="@lang('common.edit-your-comment')" data-toggle="modal"
                                                   data-target="#modal-comment-edit-{{ $comment->id }}"><i
                                                            class="pull-right btn btn-warning btn-xs {{ config('other.font-awesome') }} fa-pencil"
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

            <br>

            <div class="col-md-12">
                <form role="form" method="POST" action="{{ route('comment_article', ['id' => $article->id]) }}">
                    @csrf
                    <div class="form-group">
                        <label for="content">@lang('common.your-comment'):</label>
                        <span class="badge-extra">BBCode @lang('common.is-allowed')</span>
                        <textarea id="content" name="content" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">@lang('common.submit')</button>
                    <label class="radio-inline"><strong>@lang('common.anonymous') @lang('common.comment')
                            :</strong></label>
                    <label>
                        <input type="radio" value="1" name="anonymous">
                    </label> @lang('common.yes')
                    <label>
                        <input type="radio" value="0" checked="checked" name="anonymous">
                    </label> @lang('common.no')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce('script') }}">
      $(document).ready(function () {

        $('#content').wysibb({});
      })
    </script>
@endsection
