@extends('layout.default')

@section('title')
    <title>{{ $user->username }} - @lang('user.notification') - @lang('common.members') - {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_notification', ['username' => $user->username]) }}" itemprop="url"
            class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.notification')
                @lang('user.settings')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            @include('user.buttons.settings')
            <div class="header gradient red">
                <div class="inner_content">
                    <h1>{{ $user->username }} @lang('user.notification-settings')</h1>
                </div>
            </div>
            <div class="container-fluid p-0 some-padding">
                <ul class="nav nav-tabs" role="tablist" id="basetabs">
                    <li class="active"><a href="#account_tab" data-toggle="tab">Account</a></li>
                    <li><a href="#bon_tab" data-toggle="tab">BON</a></li>
                    <li><a href="#following_tab" data-toggle="tab">Followed User</a></li>
                    <li><a href="#forum_tab" data-toggle="tab">Forum</a></li>
                    <li><a href="#request_tab" data-toggle="tab">Request</a></li>
                    <li><a href="#subscription_tab" data-toggle="tab">Subscription</a></li>
                    <li><a href="#torrent_tab" data-toggle="tab">Torrent</a></li>
                    <li><a href="#mention_tab" data-toggle="tab">@Mention</a></li>
                </ul>
                <div class="tab-content">
                    <br>
                    <div role="tabpanel" class="tab-pane active" id="account_tab">
                        <form role="form" method="POST"
                            action="{{ route('notification_account', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>@lang('user.account-notification'):</h3>
                                <div class="help-block">@lang('user.account-notification-help').</div>
                                <hr>
                                <div class="form-group">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.account-notification-follow').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_account_follow == 1))
                                                <label>
                                                    <input type="checkbox" name="show_account_follow" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_account_follow" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.account-notification-unfollow').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_account_unfollow == 1))
                                                <label>
                                                    <input type="checkbox" name="show_account_unfollow" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_account_unfollow" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <h3>@lang('user.notification-from-account'):</h3>
                                    <div class="help-block">@lang('user.notification-from-account-help').</div>
                                    <hr>
                                    <div class="form-group">
                                        @foreach(\App\Models\Role::orderBy('position')->get() as $group)
                                            @if(in_array($group->slug, ['root', 'sudo', 'admin', 'moderator', 'coder', 'bot', 'internal', 'fls'] ))
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        <label>
                                                            <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                   CHECKED DISABLED/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @else
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        @if(!$user->notification || !$user->notification->json_account_groups ||
                                                            $group->isAllowed($user->notification->json_account_groups,$group->id))
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                    CHECKED />
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}" />
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="well text-center">
                                <button type="submit" class="btn btn-primary">@lang('common.save')
                                    @lang('user.account-notification')</button>
                            </div>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="following_tab">
                        <form role="form" method="POST"
                            action="{{ route('notification_following', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>@lang('user.following-notification'):</h3>
                                <div class="help-block">@lang('user.following-notification-help').</div>
                                <hr>
                                <div class="form-group">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.following-notification-upload').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_following_upload == 1))
                                                <label>
                                                    <input type="checkbox" name="show_following_upload" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_following_upload" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <h3>@lang('user.notification-from-following'):</h3>
                                    <div class="help-block">@lang('user.notification-from-following-help').</div>
                                    <hr>
                                    <div class="form-group">
                                        @foreach(\App\Models\Role::orderBy('position')->get() as $group)
                                            @if(in_array($group->slug, ['root', 'sudo', 'admin', 'moderator', 'coder', 'bot', 'internal', 'fls'] ))
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        <label>
                                                            <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                   CHECKED DISABLED/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @else
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        @if(!$user->notification || !$user->notification->json_following_groups ||
                                                            $group->isAllowed($user->notification->json_following_groups,$group->id))
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                    CHECKED />
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}" />
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="well text-center">
                                <button type="submit" class="btn btn-primary">@lang('common.save')
                                    @lang('user.following-notification')</button>
                            </div>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="subscription_tab">
                        <form role="form" method="POST"
                            action="{{ route('notification_subscription', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>@lang('user.subscription-notification'):</h3>
                                <div class="help-block">@lang('user.subscription-notification-help').</div>
                                <hr>
                                <div class="form-group">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.subscription-notification-topic').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_subscription_topic == 1))
                                                <label>
                                                    <input type="checkbox" name="show_subscription_topic" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_subscription_topic" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.subscription-notification-forum').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_subscription_forum == 1))
                                                <label>
                                                    <input type="checkbox" name="show_subscription_forum" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_subscription_forum" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <h3>@lang('user.notification-from-subscription'):</h3>
                                    <div class="help-block">@lang('user.notification-from-subscription-help').</div>
                                    <hr>
                                    <div class="form-group">
                                        @foreach(\App\Models\Role::orderBy('position')->get() as $group)
                                            @if(in_array($group->slug, ['root', 'sudo', 'admin', 'moderator', 'coder', 'bot', 'internal', 'fls'] ))
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        <label>
                                                            <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                   CHECKED DISABLED/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @else
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        @if(!$user->notification || !$user->notification->json_subscription_groups
                                                            ||
                                                            $group->isAllowed($user->notification->json_subscription_groups,$group->id))
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                    CHECKED />
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}" />
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="well text-center">
                                <button type="submit" class="btn btn-primary">@lang('common.save')
                                    @lang('user.subscription-notification')</button>
                            </div>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="forum_tab">
                        <form role="form" method="POST"
                            action="{{ route('notification_forum', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>@lang('user.forum-notification'):</h3>
                                <div class="help-block">@lang('user.forum-notification-help').</div>
                                <hr>
                                <div class="form-group">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.forum-notification-topic').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_forum_topic == 1))
                                                <label>
                                                    <input type="checkbox" name="show_forum_topic" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_forum_topic" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <h3>@lang('user.notification-from-forum'):</h3>
                                    <div class="help-block">@lang('user.notification-from-forum-help').</div>
                                    <hr>
                                    <div class="form-group">
                                        @foreach(\App\Models\Role::orderBy('position')->get() as $group)
                                            @if(in_array($group->slug, ['root', 'sudo', 'admin', 'moderator', 'coder', 'bot', 'internal', 'fls'] ))
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        <label>
                                                            <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                   CHECKED DISABLED/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @else
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        @if(!$user->notification || !$user->notification->json_forum_groups ||
                                                            $group->isAllowed($user->notification->json_forum_groups,$group->id))
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                    CHECKED />
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}" />
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="well text-center">
                                <button type="submit" class="btn btn-primary">@lang('common.save')
                                    @lang('user.forum-notification')</button>
                            </div>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="request_tab">
                        <form role="form" method="POST"
                            action="{{ route('notification_request', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>@lang('user.request-notification'):</h3>
                                <div class="help-block">@lang('user.request-notification-help').</div>
                                <hr>
                                <div class="form-group">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.request-notification-fill').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_request_fill == 1))
                                                <label>
                                                    <input type="checkbox" name="show_request_fill" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_request_fill" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.request-notification-fill-approve').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_request_fill_approve == 1))
                                                <label>
                                                    <input type="checkbox" name="show_request_fill_approve" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_request_fill_approve" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.request-notification-fill-reject').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_request_fill_reject == 1))
                                                <label>
                                                    <input type="checkbox" name="show_request_fill_reject" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_request_fill_reject" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.request-notification-claim').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_request_claim == 1))
                                                <label>
                                                    <input type="checkbox" name="show_request_claim" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_request_claim" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.request-notification-unclaim').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_request_unclaim == 1))
                                                <label>
                                                    <input type="checkbox" name="show_request_unclaim" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_request_unclaim" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.request-notification-comment').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_request_comment == 1))
                                                <label>
                                                    <input type="checkbox" name="show_request_comment" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_request_comment" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.request-notification-bounty').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_request_bounty == 1))
                                                <label>
                                                    <input type="checkbox" name="show_request_bounty" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_request_bounty" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <h3>@lang('user.notification-from-request'):</h3>
                                    <div class="help-block">@lang('user.notification-from-request-help').</div>
                                    <hr>
                                    <div class="form-group">
                                        @foreach(\App\Models\Role::orderBy('position')->get() as $group)
                                            @if(in_array($group->slug, ['root', 'sudo', 'admin', 'moderator', 'coder', 'bot', 'internal', 'fls'] ))
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        <label>
                                                            <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                   CHECKED DISABLED/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @else
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        @if(!$user->notification || !$user->notification->json_request_groups ||
                                                            $group->isAllowed($user->notification->json_request_groups,$group->id))
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                    CHECKED />
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}" />
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="well text-center">
                                <button type="submit" class="btn btn-primary">@lang('common.save')
                                    @lang('user.request-notification')</button>
                            </div>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="torrent_tab">
                        <form role="form" method="POST"
                            action="{{ route('notification_torrent', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>@lang('user.torrent-notification'):</h3>
                                <div class="help-block">@lang('user.torrent-notification-help').</div>
                                <hr>
                                <div class="form-group">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.torrent-notification-comment').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_torrent_comment == 1))
                                                <label>
                                                    <input type="checkbox" name="show_torrent_comment" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_torrent_comment" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.torrent-notification-thank').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_torrent_thank == 1))
                                                <label>
                                                    <input type="checkbox" name="show_torrent_thank" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_torrent_thank" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.torrent-notification-tip').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_torrent_tip == 1))
                                                <label>
                                                    <input type="checkbox" name="show_torrent_tip" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_torrent_tip" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <h3>@lang('user.notification-from-torrent'):</h3>
                                    <div class="help-block">@lang('user.notification-from-torrent-help').</div>
                                    <hr>
                                    <div class="form-group">
                                        @foreach(\App\Models\Role::orderBy('position')->get() as $group)
                                            @if(in_array($group->slug, ['root', 'sudo', 'admin', 'moderator', 'coder', 'bot', 'internal', 'fls'] ))
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        <label>
                                                            <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                   CHECKED DISABLED/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @else
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        @if(!$user->notification || !$user->notification->json_torrent_groups ||
                                                            $group->isAllowed($user->notification->json_torrent_groups,$group->id))
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                    CHECKED />
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}" />
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="well text-center">
                                <button type="submit" class="btn btn-primary">@lang('common.save')
                                    @lang('user.torrent-notification')</button>
                            </div>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="bon_tab">
                        <form role="form" method="POST"
                            action="{{ route('notification_bon', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>@lang('user.bon-notification'):</h3>
                                <div class="help-block">@lang('user.bon-notification-help').</div>
                                <hr>
                                <div class="form-group">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.bon-notification-gift').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_bon_gift == 1))
                                                <label>
                                                    <input type="checkbox" name="show_bon_gift" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_bon_gift" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <h3>@lang('user.notification-from-bon'):</h3>
                                    <div class="help-block">@lang('user.notification-from-bon-help').</div>
                                    <hr>
                                    <div class="form-group">
                                        @foreach(\App\Models\Role::orderBy('position')->get() as $group)
                                            @if(in_array($group->slug, ['root', 'sudo', 'admin', 'moderator', 'coder', 'bot', 'internal', 'fls'] ))
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        <label>
                                                            <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                   CHECKED DISABLED/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @else
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        @if(!$user->notification || !$user->notification->json_bon_groups ||
                                                            $group->isAllowed($user->notification->json_bon_groups,$group->id))
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                    CHECKED />
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}" />
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="well text-center">
                                <button type="submit" class="btn btn-primary">@lang('common.save')
                                    @lang('user.bon-notification')</button>
                            </div>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="mention_tab">
                        <form role="form" method="POST"
                            action="{{ route('notification_mention', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>@lang('user.mention-notification'):</h3>
                                <div class="help-block">@lang('user.mention-notification-help').</div>
                                <hr>
                                <div class="form-group">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.mention-notification-article-comment').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_mention_article_comment == 1))
                                                <label>
                                                    <input type="checkbox" name="show_mention_article_comment" value="1"
                                                        CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_mention_article_comment" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.mention-notification-request-comment').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_mention_request_comment == 1))
                                                <label>
                                                    <input type="checkbox" name="show_mention_request_comment" value="1"
                                                        CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_mention_request_comment" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.mention-notification-torrent-comment').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_mention_torrent_comment == 1))
                                                <label>
                                                    <input type="checkbox" name="show_mention_torrent_comment" value="1"
                                                        CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_mention_torrent_comment" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <div class="button-holder">
                                        <div class="button-left">
                                            @lang('user.mention-notification-forum-post').
                                        </div>
                                        <div class="button-right">
                                            @if(!$user->notification || ($user->notification &&
                                                $user->notification->show_mention_forum_post == 1))
                                                <label>
                                                    <input type="checkbox" name="show_mention_forum_post" value="1" CHECKED />
                                                </label>
                                            @else
                                                <label>
                                                    <input type="checkbox" name="show_mention_forum_post" value="1" />
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="some-padding">
                                    <h3>@lang('user.notification-from-mention'):</h3>
                                    <div class="help-block">@lang('user.notification-from-mention-help').</div>
                                    <hr>
                                    <div class="form-group">
                                        @foreach(\App\Models\Role::orderBy('position')->get() as $group)
                                            @if(in_array($group->slug, ['root', 'sudo', 'admin', 'moderator', 'coder', 'bot', 'internal', 'fls'] ))
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        <label>
                                                            <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                   CHECKED DISABLED/>
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @else
                                                <div class="button-holder">
                                                    <div class="button-left">
                                                        {{ $group->name }}
                                                    </div>
                                                    <div class="button-right">
                                                        @if(!$user->notification || !$user->notification->json_mention_groups ||
                                                            $group->isAllowed($user->notification->json_mention_groups,$group->id))
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}"
                                                                    CHECKED />
                                                            </label>
                                                        @else
                                                            <label>
                                                                <input type="checkbox" name="approved[]" value="{{ $group->id }}" />
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr class="some-padding">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="well text-center">
                                <button type="submit" class="btn btn-primary">@lang('common.save')
                                    @lang('user.mention-notification')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascripts')
    <script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce('script') }}">
        $(window).on("load", function() {
            loadTab();
        });

        function loadTab() {
            if (window.location.hash && window.location.hash == "#account_tab") {
                $('#basetabs a[href="#account_tab"]').tab('show');
            }
            if (window.location.hash && window.location.hash == "#bon_tab") {
                $('#basetabs a[href="#bon_tab"]').tab('show');
            }
            if (window.location.hash && window.location.hash == "#following_tab") {
                $('#basetabs a[href="#following_tab"]').tab('show');
            }
            if (window.location.hash && window.location.hash == "#forum_tab") {
                $('#basetabs a[href="#forum_tab"]').tab('show');
            }
            if (window.location.hash && window.location.hash == "#torrent_tab") {
                $('#basetabs a[href="#torrent_tab"]').tab('show');
            }
            if (window.location.hash && window.location.hash == "#mention_tab") {
                $('#basetabs a[href="#mention_tab"]').tab('show');
            }
            if (window.location.hash && window.location.hash == "#subscription_tab") {
                $('#basetabs a[href="#subscription_tab"]').tab('show');
            }
            if (window.location.hash && window.location.hash == "#request_tab") {
                $('#basetabs a[href="#request_tab"]').tab('show');
            }
        }

    </script>
@endsection
