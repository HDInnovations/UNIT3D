@extends('layout.with-main')

@section('title')
    <title>
        {{ $user->username }} - {{ __('user.notification') }} - {{ __('common.members') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a
            href="{{ route('users.general_settings.edit', ['user' => $user]) }}"
            class="breadcrumb__link"
        >
            {{ __('user.settings') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.notification') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-notification-setting--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.notification') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('users.notification_settings.update', ['user' => $user]) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PATCH')
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('user.follow') }}</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_account_follow" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_account_follow"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_account_follow)
                            />
                            {{ __('user.account-notification-follow') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_account_unfollow" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_account_unfollow"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_account_unfollow)
                            />
                            {{ __('user.account-notification-unfollow') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('bon.bon') }}</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_bon_gift" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_bon_gift"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_bon_gift)
                            />
                            {{ __('user.bon-notification-gift') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('user.followers') }}</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_following_upload" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_following_upload"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_following_upload)
                            />
                            {{ __('user.following-notification-upload') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('forum.forums') }}</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_forum_topic" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_forum_topic"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_forum_topic)
                            />
                            {{ __('user.forum-notification-topic') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('request.requests') }}</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_request_fill" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_request_fill"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_request_fill)
                            />
                            {{ __('user.request-notification-fill') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_request_fill_approve" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_request_fill_approve"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_request_fill_approve)
                            />
                            {{ __('user.request-notification-fill-approve') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_request_fill_reject" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_request_fill_reject"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_request_fill_reject)
                            />
                            {{ __('user.request-notification-fill-reject') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_request_claim" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_request_claim"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_request_claim)
                            />
                            {{ __('user.request-notification-claim') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_request_unclaim" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_request_unclaim"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_request_unclaim)
                            />
                            {{ __('user.request-notification-unclaim') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_request_comment" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_request_comment"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_request_comment)
                            />
                            {{ __('user.request-notification-comment') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_request_bounty" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_request_bounty"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_request_bounty)
                            />
                            {{ __('user.request-notification-bounty') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('common.subscriptions') }}</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_subscription_topic" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_subscription_topic"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_subscription_topic)
                            />
                            {{ __('user.subscription-notification-topic') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_subscription_forum" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_subscription_forum"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_subscription_forum)
                            />
                            {{ __('user.subscription-notification-forum') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('torrent.torrents') }}</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_torrent_comment" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_torrent_comment"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_torrent_comment)
                            />
                            {{ __('user.torrent-notification-comment') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_torrent_thank" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_torrent_thank"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_torrent_thank)
                            />
                            {{ __('user.torrent-notification-thank') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_torrent_tip" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_torrent_tip"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_torrent_tip)
                            />
                            {{ __('user.torrent-notification-tip') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Mentions</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_mention_article_comment" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_mention_article_comment"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_mention_article_comment)
                            />
                            {{ __('user.mention-notification-article-comment') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_mention_request_comment" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_mention_request_comment"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_mention_request_comment)
                            />
                            {{ __('user.mention-notification-request-comment') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_mention_torrent_comment" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_mention_torrent_comment"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_mention_torrent_comment)
                            />
                            {{ __('user.mention-notification-torrent-comment') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="show_mention_forum_post" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="show_mention_forum_post"
                                value="1"
                                @checked($user->notification === null || $user->notification?->show_mention_forum_post)
                            />
                            {{ __('user.mention-notification-forum-post') }}
                        </label>
                    </p>
                </fieldset>
                <h3>Block all notifications from the selected groups.</h3>
                <div class="form__group">
                    <div class="data-table-wrapper">
                        <table
                            class="data-table data-table--checkbox-grid"
                            x-data="checkboxGrid"
                        >
                            <thead>
                                <tr>
                                    <th x-bind="columnHeader">{{ __('common.group') }}</th>
                                    <th x-bind="columnHeader">{{ __('user.follow') }}</th>
                                    <th x-bind="columnHeader">{{ __('bon.bon') }}</th>
                                    <th x-bind="columnHeader">{{ __('user.followers') }}</th>
                                    <th x-bind="columnHeader">{{ __('forum.forums') }}</th>
                                    <th x-bind="columnHeader">{{ __('request.requests') }}</th>
                                    <th x-bind="columnHeader">
                                        {{ __('common.subscriptions') }}
                                    </th>
                                    <th x-bind="columnHeader">{{ __('torrent.torrents') }}</th>
                                    <th x-bind="columnHeader">Mentions</th>
                                </tr>
                            </thead>
                            <tbody x-ref="tbody">
                                @foreach ($groups as $group)
                                    <tr>
                                        <th x-bind="rowHeader">
                                            {{ $group->name }}
                                        </th>
                                        @foreach ([
                                            'json_account_groups',
                                            'json_bon_groups',
                                            'json_following_groups',
                                            'json_forum_groups',
                                            'json_request_groups',
                                            'json_subscription_groups',
                                            'json_torrent_groups',
                                            'json_mention_groups',
                                        ] as $setting)
                                            <td x-bind="cell">
                                                <input
                                                    class="form__checkbox"
                                                    type="checkbox"
                                                    name="{{ $setting }}[]"
                                                    value="{{ $group->id }}"
                                                    @checked($user->notification !== null && \in_array($group->id, $user->notification->$setting, true))
                                                />
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <h3>Override all notifications.</h3>
                <p class="form__group">
                    <label class="form__label">
                        <input type="hidden" name="block_notifications" value="0" />
                        <input
                            class="form__checkbox"
                            type="checkbox"
                            value="1"
                            name="block_notifications"
                            @checked($user->notification?->block_notifications)
                        />
                        Block all notifications.
                    </label>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.save') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
