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
        <h2 class="panel__heading">{{ __('staff-interface.group-heading-create') }}</h2>
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
                    <label class="form__label form__label--floating" for="level">
                        {{ __('staff-interface.group-level') }}
                    </label>
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
                        {{ __('staff-interface.group-dl-slots') }}
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
                        {{ __('staff-interface.group-color-hint') }}
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
                        {{ __('staff-interface.group-icon-hint') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="effect"
                        class="form__text"
                        type="text"
                        name="group[effect]"
                        value="none"
                        placeholder="{{ __('staff-interface.group-effect-placeholder') }}"
                    />
                    <label class="form__label form__label--floating" for="effect">
                        {{ __('staff-interface.group-effect-hint') }}
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
                    <label class="form__label" for="is_uploader">
                        {{ __('staff-interface.group-uploader') }}
                    </label>
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
                    <label class="form__label" for="is_internal">{{ __('common.internal') }}</label>
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
                    <label class="form__label" for="is_editor">
                        {{ __('staff-interface.group-editor') }}
                    </label>
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
                    <label class="form__label" for="is_torrent_modo">
                        {{ __('staff-interface.group-torrent-modo') }}
                    </label>
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
                    <label class="form__label" for="is_modo">
                        {{ __('staff-interface.group-modo') }}
                    </label>
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
                    <label class="form__label" for="is_admin">
                        {{ __('staff-interface.group-admin') }}
                    </label>
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
                    <label class="form__label" for="is_owner">
                        {{ __('staff-interface.group-owner') }}
                    </label>
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
                    <label class="form__label" for="is_trusted">
                        {{ __('staff-interface.group-trusted') }}
                    </label>
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
                    <label class="form__label" for="is_immune">
                        {{ __('staff-interface.group-immune') }}
                    </label>
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
                    <label class="form__label" for="is_freeleech">
                        {{ __('torrent.freeleech') }}
                    </label>
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
                    <label class="form__label" for="is_double_upload">
                        {{ __('torrent.double-upload') }}
                    </label>
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
                    <label class="form__label" for="is_refundable">
                        {{ __('staff-interface.group-refundable-download') }}
                    </label>
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
                    <label class="form__label" for="is_incognito">
                        {{ __('staff-interface.group-incognito') }}
                    </label>
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
                    <label class="form__label" for="can_chat">{{ __('common.chat') }}</label>
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
                    <label class="form__label" for="can_comment">
                        {{ __('common.comment') }}
                    </label>
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
                    <label class="form__label" for="can_invite">
                        {{ __('staff-interface.group-invite') }}
                    </label>
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
                    <label class="form__label" for="can_request">
                        {{ __('staff-interface.group-request') }}
                    </label>
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
                    <label class="form__label" for="can_upload">
                        {{ __('staff-interface.group-can-upload') }}
                    </label>
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
                    <label class="form__label" for="autogroup">
                        {{ __('staff-interface.group-autogroup') }}
                    </label>
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
                        {{ __('staff-interface.group-min-uploaded-required') }}
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
                        {{ __('staff-interface.group-min-ratio-required') }}
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
                        {{ __('staff-interface.group-min-age-required') }}
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
                        {{ __('staff-interface.group-min-avg-seedtime-required') }}
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
                        {{ __('staff-interface.group-min-seedsize-required') }}
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
                        {{ __('staff-interface.group-min-uploads-required') }}
                    </label>
                </p>
                <div class="form__group">
                    <label class="form__label">{{ __('staff-interface.permissions') }}</label>
                    <div class="data-table-wrapper">
                        <table
                            class="data-table data-table--checkbox-grid"
                            x-data="checkboxGrid"
                        >
                            <thead>
                                <tr>
                                    <th x-bind="columnHeader">
                                        {{ __('staff-interface.forum-category') }}
                                    </th>
                                    <th x-bind="columnHeader">{{ __('common.forum') }}</th>
                                    <th x-bind="columnHeader">
                                        {{ __('staff-interface.read-topics') }}
                                    </th>
                                    <th x-bind="columnHeader">
                                        {{ __('staff-interface.start-new-topic') }}
                                    </th>
                                    <th x-bind="columnHeader">
                                        {{ __('staff-interface.reply-to-topics') }}
                                    </th>
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
