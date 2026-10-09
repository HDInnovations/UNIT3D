@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.groups.index') }}" class="breadcrumb__link">
            {{ __('staff.groups') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-group--create')

@section('main')
    <section class="panelV2" x-data="{ autogroup: false }">
        <h2 class="panel__heading">Add new group</h2>
        <div class="panel__body">
            <form class="form" method="POST" action="{{ route('staff.groups.store') }}">
                @csrf
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="group[name]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="description"
                        class="form__text"
                        type="text"
                        name="group[description]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="description">
                        {{ __('common.description') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        type="text"
                        name="group[position]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="level"
                        class="form__text"
                        type="text"
                        name="group[level]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="level">Level</label>
                </p>
                <p class="form__group">
                    <input
                        id="download_slots"
                        class="form__text"
                        type="text"
                        name="group[download_slots]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="download_slots">
                        DL slots
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="color"
                        class="form__text"
                        type="text"
                        name="group[color]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="color">
                        Color (e.g. #ff0000)
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="icon"
                        class="form__text"
                        type="text"
                        name="group[icon]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="icon">
                        FontAwesome icon (e.g. fas fa-user)
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="effect"
                        class="form__text"
                        type="text"
                        name="group[effect]"
                        value="none"
                        placeholder="GIF effect"
                    />
                    <label class="form__label form__label--floating" for="effect">
                        Effect (e.g. url(/img/sparkels.gif))
                    </label>
                </p>
                <p class="form__group">
                    <input name="group[is_uploader]" type="hidden" value="0" />
                    <input
                        id="is_uploader"
                        class="form__checkbox"
                        name="group[is_uploader]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_uploader">Uploader</label>
                </p>
                <p class="form__group">
                    <input name="group[is_internal]" type="hidden" value="0" />
                    <input
                        id="is_internal"
                        class="form__checkbox"
                        name="group[is_internal]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_internal">Internal</label>
                </p>
                <p class="form__group">
                    <input name="group[is_editor]" type="hidden" value="0" />
                    <input
                        id="is_editor"
                        class="form__checkbox"
                        name="group[is_editor]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_editor">Editor</label>
                </p>
                <p class="form__group">
                    <input name="group[is_torrent_modo]" type="hidden" value="0" />
                    <input
                        id="is_torrent_modo"
                        class="form__checkbox"
                        name="group[is_torrent_modo]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_torrent_modo">Torrent modo</label>
                </p>

                <p class="form__group">
                    <input name="group[is_modo]" type="hidden" value="0" />
                    <input
                        id="is_modo"
                        class="form__checkbox"
                        name="group[is_modo]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_modo">Modo</label>
                </p>
                <p class="form__group">
                    <input name="group[is_admin]" type="hidden" value="0" />
                    <input
                        id="is_admin"
                        class="form__checkbox"
                        name="group[is_admin]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_admin">Admin</label>
                </p>
                <p class="form__group">
                    <input name="group[is_owner]" type="hidden" value="0" />
                    <input
                        id="is_owner"
                        class="form__checkbox"
                        name="group[is_owner]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_owner">Owner</label>
                </p>
                <p class="form__group">
                    <input name="group[is_trusted]" type="hidden" value="0" />
                    <input
                        id="is_trusted"
                        class="form__checkbox"
                        name="group[is_trusted]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_trusted">Trusted</label>
                </p>
                <p class="form__group">
                    <input name="group[is_immune]" type="hidden" value="0" />
                    <input
                        id="is_immune"
                        class="form__checkbox"
                        name="group[is_immune]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_immune">Immune</label>
                </p>
                <p class="form__group">
                    <input name="group[is_freeleech]" type="hidden" value="0" />
                    <input
                        id="is_freeleech"
                        class="form__checkbox"
                        name="group[is_freeleech]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_freeleech">Freeleech</label>
                </p>
                <p class="form__group">
                    <input name="group[is_double_upload]" type="hidden" value="0" />
                    <input
                        id="is_double_upload"
                        class="form__checkbox"
                        name="group[is_double_upload]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_double_upload">Double upload</label>
                </p>
                <p class="form__group">
                    <input name="group[is_refundable]" type="hidden" value="0" />
                    <input
                        id="is_refundable"
                        class="form__checkbox"
                        name="group[is_refundable]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_refundable">Refundable download</label>
                </p>
                <p class="form__group">
                    <input name="group[is_incognito]" type="hidden" value="0" />
                    <input
                        id="is_incognito"
                        class="form__checkbox"
                        name="group[is_incognito]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="is_incognito">Incognito</label>
                </p>
                <p class="form__group">
                    <input name="group[can_chat]" type="hidden" value="0" />
                    <input
                        id="can_chat"
                        class="form__checkbox"
                        name="group[can_chat]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="can_chat">Chat</label>
                </p>
                <p class="form__group">
                    <input name="group[can_comment]" type="hidden" value="0" />
                    <input
                        id="can_comment"
                        class="form__checkbox"
                        name="group[can_comment]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="can_comment">Comment</label>
                </p>
                <p class="form__group">
                    <input name="group[can_invite]" type="hidden" value="0" />
                    <input
                        id="can_invite"
                        class="form__checkbox"
                        name="group[can_invite]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="can_invite">Invite</label>
                </p>
                <p class="form__group">
                    <input name="group[can_request]" type="hidden" value="0" />
                    <input
                        id="can_request"
                        class="form__checkbox"
                        name="group[can_request]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="can_request">Request</label>
                </p>
                <p class="form__group">
                    <input name="group[can_upload]" type="hidden" value="0" />
                    <input
                        id="can_upload"
                        class="form__checkbox"
                        name="group[can_upload]"
                        type="checkbox"
                        value="1"
                    />
                    <label class="form__label" for="can_upload">Upload</label>
                </p>
                <p class="form__group">
                    <input name="group[autogroup]" type="hidden" value="0" />
                    <input
                        id="autogroup"
                        class="form__checkbox"
                        name="group[autogroup]"
                        type="checkbox"
                        x-model="autogroup"
                        value="1"
                    />
                    <label class="form__label" for="autogroup">Autogroup</label>
                </p>
                <p class="form__group" x-show="autogroup" x-cloak>
                    <input
                        id="min_uploaded"
                        class="form__text"
                        type="text"
                        name="group[min_uploaded]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="min_uploaded">
                        Minimum upload required
                    </label>
                </p>
                <p class="form__group" x-show="autogroup" x-cloak>
                    <input
                        id="min_actual_uploaded"
                        class="form__text"
                        type="text"
                        name="group[min_actual_uploaded]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="min_actual_uploaded">
                        Minimum actual upload required
                    </label>
                </p>
                <p class="form__group" x-show="autogroup" x-cloak>
                    <input
                        id="min_ratio"
                        class="form__text"
                        type="text"
                        name="group[min_ratio]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="min_ratio">
                        Minimum ratio required
                    </label>
                </p>
                <p class="form__group" x-show="autogroup" x-cloak>
                    <input
                        id="min_age"
                        class="form__text"
                        type="text"
                        name="group[min_age]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="min_age">
                        Minimum age required
                    </label>
                </p>
                <p class="form__group" x-show="autogroup" x-cloak>
                    <input
                        id="min_avg_seedtime"
                        class="form__text"
                        type="text"
                        name="group[min_avg_seedtime]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="min_avg_seedtime">
                        Minimum average seedtime required
                    </label>
                </p>
                <p class="form__group" x-show="autogroup" x-cloak>
                    <input
                        id="min_seedsize"
                        class="form__text"
                        type="text"
                        name="group[min_seedsize]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="min_seedsize">
                        Minimum seedsize required
                    </label>
                </p>
                <p class="form__group" x-show="autogroup" x-cloak>
                    <input
                        id="min_uploads"
                        class="form__text"
                        type="text"
                        name="group[min_uploads]"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="min_uploads">
                        Minimum uploads required
                    </label>
                </p>
                <div class="form__group">
                    <label class="form__label">Permissions</label>
                    <div class="data-table-wrapper">
                        <table
                            class="data-table data-table--checkbox-grid"
                            x-data="checkboxGrid"
                        >
                            <thead>
                                <tr>
                                    <th x-bind="columnHeader">Forum category</th>
                                    <th x-bind="columnHeader">Forum</th>
                                    <th x-bind="columnHeader">Read topics</th>
                                    <th x-bind="columnHeader">Start new topic</th>
                                    <th x-bind="columnHeader">Reply to topics</th>
                                </tr>
                            </thead>
                            <tbody x-ref="tbody">
                                @foreach ($forumCategories as $forumCategory)
                                    @foreach ($forumCategory->forums as $forum)
                                        <tr>
                                            @if ($loop->first)
                                                <th
                                                    x-bind="rowHeader"
                                                    rowspan="{{ $forumCategory->forums->count() }}"
                                                >
                                                    {{ $forum->category->name }}
                                                </th>
                                            @else
                                                <th style="display: none"></th>
                                            @endif

                                            <th x-bind="rowHeader">
                                                {{ $forum->name }}
                                                <input
                                                    type="hidden"
                                                    name="permissions[{{ $forum->id }}][forum_id]"
                                                    value="{{ $forum->id }}"
                                                />
                                            </th>
                                            <td x-bind="cell">
                                                <input
                                                    type="hidden"
                                                    name="permissions[{{ $forum->id }}][read_topic]"
                                                    value="0"
                                                />
                                                <input
                                                    type="checkbox"
                                                    name="permissions[{{ $forum->id }}][read_topic]"
                                                    value="1"
                                                />
                                            </td>
                                            <td x-bind="cell">
                                                <input
                                                    type="hidden"
                                                    name="permissions[{{ $forum->id }}][start_topic]"
                                                    value="0"
                                                />
                                                <input
                                                    type="checkbox"
                                                    name="permissions[{{ $forum->id }}][start_topic]"
                                                    value="1"
                                                />
                                            </td>
                                            <td x-bind="cell">
                                                <input
                                                    type="hidden"
                                                    name="permissions[{{ $forum->id }}][reply_topic]"
                                                    value="0"
                                                />
                                                <input
                                                    type="checkbox"
                                                    name="permissions[{{ $forum->id }}][reply_topic]"
                                                    value="1"
                                                />
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.add') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
