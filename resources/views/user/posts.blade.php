@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.posts') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_posts', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.posts')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        @if (!auth()->user()->isAllowed($user,'forum','show_post'))
            <div class="container pl-0 text-center">
                <div class="jumbotron shadowed">
                    <div class="container">
                        <h1 class="mt-5 text-center">
                            <i class="{{ config('other.font-awesome') }} fa-times text-danger"></i>@lang('user.private-profile')
                        </h1>
                        <div class="separator"></div>
                        <p class="text-center">@lang('user.not-authorized')</p>
                    </div>
                </div>
            </div>
        @else
            <div class="block">
                @if (auth()->user()->id == $user->id || auth()->user()->group->is_modo)
                    @include('user.buttons.forum')
                @else
                    @include('user.buttons.public')
                @endif
                <div class="header gradient blue">
                    <div class="inner_content">
                        <h1>
                            {{ $user->username }} @lang('user.posts')
                        </h1>
                    </div>
                </div>

                <div class="forum-categories">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>@lang('forum.forum')</th>
                                <th>@lang('forum.topic')</th>
                                <th>@lang('forum.author')</th>
                                <th>@lang('forum.stats')</th>
                                <th>@lang('forum.last-post-info')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $r)
                                @if ($r->topic->viewable())
                                    <tr>
                                        <td class="f-display-topic-icon"><span
                                                class="badge-extra text-bold">{{ $r->topic->forum->name }}</span></td>
                                        <td class="f-display-topic-title">
                                            <strong><a
                                                    href="{{ route('forum_topic', ['id' => $r->topic->id]) }}">{{ $r->topic->name }}</a></strong>
                                            @if ($r->topic->state == "close") <span
                                                    class='label label-sm label-default'>{{ strtoupper(trans('forum.closed')) }}</span>
                                            @endif
                                            @if ($r->topic->approved == "1") <span
                                                    class='label label-sm label-success'>{{ strtoupper(trans('forum.approved')) }}</span>
                                            @endif
                                            @if ($r->topic->denied == "1") <span
                                                    class='label label-sm label-danger'>{{ strtoupper(trans('forum.denied')) }}</span>
                                            @endif
                                            @if ($r->topic->solved == "1") <span
                                                class='label label-sm label-info'>{{ strtoupper(trans('forum.solved')) }}</span> @endif
                                            @if ($r->topic->invalid == "1") <span
                                                    class='label label-sm label-warning'>{{ strtoupper(trans('forum.invalid')) }}</span>
                                            @endif
                                            @if ($r->topic->bug == "1") <span
                                                class='label label-sm label-danger'>{{ strtoupper(trans('forum.bug')) }}</span> @endif
                                            @if ($r->topic->suggestion == "1") <span
                                                    class='label label-sm label-primary'>{{ strtoupper(trans('forum.suggestion')) }}</span>
                                            @endif
                                        </td>
                                        <td class="f-display-topic-started"><a
                                                href="{{ route('users.show', ['username' => $r->topic->first_post_user_username]) }}">{{ $r->topic->first_post_user_username }}</a>
                                        </td>
                                        <td class="f-display-topic-stats">
                                            {{ $r->topic->num_post - 1 }} @lang('forum.replies')
                                            \ {{ $r->topic->views }} @lang('forum.views')
                                        </td>
                                        <td class="f-display-topic-last-post">
                                            <a
                                                href="{{ route('users.show', ['username' => $r->topic->last_post_user_username]) }}">{{ $r->topic->last_post_user_username }}</a>,
                                            <time datetime="{{ date('d-m-Y h:m', strtotime($r->topic->updated_at)) }}">
                                                {{ date('M d Y', strtotime($r->topic->updated_at)) }}
                                            </time>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="some-padding button-padding">
                                            <div class="topic-posts button-padding">
                                                <div class="post" id="post-{{ $r->id }}">
                                                    <div class="button-holder">
                                                        <div class="button-left">
                                                            <a href="{{ route('users.show', ['username' => $r->user->username]) }}"
                                                                class="post-info-username"
                                                                style="color:{{ $r->user->group->color }}; display:inline;">{{ $r->user->username }}</a>
                                                            @ {{ date('M d Y h:i:s', $r->created_at->getTimestamp()) }}
                                                        </div>
                                                        <div class="button-right">
                                                            <a class="text-bold"
                                                                href="{{ route('forum_topic', ['id' => $r->topic->id]) }}?page={{ $r->getPageNumber() }}#post-{{ $r->id }}">#{{ $r->id }}</a>
                                                        </div>
                                                    </div>
                                                    <hr class="some-margin">
                                                    @joypixels($r->getContentHtml())
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center col-md-12">
                    {{ $results->links() }}
                </div>

            </div>
        @endif
    </div>
@endsection
