@extends('layout.default')

@section('title')
    <title>{{ $user->username }} - Settings - @lang('common.members') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_settings', ['username' => $user->username]) }}" itemprop="url"
            class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.general')
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
                    <h1>{{ $user->username }} @lang('user.general-settings')</h1>
                </div>
            </div>
            <div class="container-fluid p-0 some-padding">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#general" data-toggle="tab">General</a></li>
                </ul>
                <div class="tab-content">
                    <br>
                        <form role="form" method="POST"
                            action="{{ route('change_settings', ['username' => $user->username]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="well">
                                <h3>Language</h3>
                                <hr>
                                <div class="form-group">
                                    <label for="language" class="control-label">Language</label>
                                    <select class="form-control" id="language" name="language">
                                        @foreach (App\Models\Language::allowed() as $code => $name)
                                            <option @if (auth()->user()->locale == $code) selected @endif value="{{ $code }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="well">
                                <h3>Style</h3>
                                <hr>
                                <div class="form-group">
                                    <label for="theme" class="control-label">Theme</label>
                                    <select class="form-control" id="theme" name="theme">
                                        <option @if ($user->style == 0) selected @endif value="0">Light Theme</option>
                                        <option @if ($user->style == 1) selected @endif value="1">Galactic Theme</option>
                                        <option @if ($user->style == 2) selected @endif value="2">Dark Blue Theme</option>
                                        <option @if ($user->style == 3) selected @endif value="3">Dark Green Theme</option>
                                        <option @if ($user->style == 4) selected @endif value="4">Dark Pink Theme</option>
                                        <option @if ($user->style == 5) selected @endif value="5">Dark Purple Theme</option>
                                        <option @if ($user->style == 6) selected @endif value="6">Dark Red Theme</option>
                                        <option @if ($user->style == 7) selected @endif value="7">Dark Teal Theme</option>
                                        <option @if ($user->style == 8) selected @endif value="8">Dark Yellow Theme</option>
                                        <option @if ($user->style == 9) selected @endif value="9">Cosmic Void Theme</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="custom_css" class="control-label">External CSS Stylesheet</label>
                                    <label>
                                        <input type="text" name="custom_css" class="form-control"
                                        value="@if ($user->custom_css) {{ $user->custom_css }}@endif"
                                            placeholder="CSS URL">
                                    </label>
                                </div>
                                <label for="sidenav" class="control-label">Side Navigation</label>
                                <div class="radio-inline">
                                    <label><input type="radio" name="sidenav" @if ($user->nav == 1) checked
                                        @endif value="1">Expanded</label>
                                </div>
                                <div class="radio-inline">
                                    <label><input type="radio" name="sidenav" @if ($user->nav == 0) checked
                                        @endif value="0">Compact</label>
                                </div>
                            </div>

                            <div class="well">
                                <h3>Chat</h3>
                                <hr>
                                <label for="hidden" class="control-label">Language Censor Chat?</label>
                                <div class="radio-inline">
                                    <label><input type="radio" name="censor" @if ($user->censor == 1) checked
                                        @endif value="1">@lang('common.yes')</label>
                                </div>
                                <div class="radio-inline">
                                <label><input type="radio" name="censor" @if ($user->censor == 0) checked @endif
                                        value="0">@lang('common.no')</label>
                                </div>
                                <br>
                                <br>
                                <label for="hidden" class="control-label">Hide Chat?</label>
                                <div class="radio-inline">
                                    <label><input type="radio" name="chat_hidden" @if ($user->chat_hidden == 1) checked
                                        @endif value="1">@lang('common.yes')</label>
                                </div>
                                <div class="radio-inline">
                                    <label><input type="radio" name="chat_hidden" @if ($user->chat_hidden == 0) checked
                                        @endif value="0">@lang('common.no')</label>
                                </div>
                            </div>

                            <div class="well">
                                <h3>Torrent</h3>
                                <hr>
                                <label for="torrent_layout" class="control-label">Default Torrent Layout?</label>
                                <select class="form-control" id="torrent_layout" name="torrent_layout">
                                <option @if ($user->torrent_layout == 0) selected @endif value="0">Torrent List</option>
                                <option @if ($user->torrent_layout == 1) selected @endif value="1">Torrent Grouping
                                    </option>
                                <option @if ($user->torrent_layout == 2) selected @endif value="2">Torrent Cards
                                    </option>
                                </select>
                                <br>
                                <label for="poster" class="control-label">Show Posters On Torrent List View?</label>
                                <div class="radio-inline">
                                    <label><input type="radio" name="show_poster" @if ($user->show_poster == 1) checked
                                        @endif value="1">@lang('common.yes')</label>
                                </div>
                                <div class="radio-inline">
                                    <label><input type="radio" name="show_poster" @if ($user->show_poster == 0) checked
                                        @endif value="0">@lang('common.no')</label>
                                </div>
                                <br>
                                <br>
                                <label for="ratings" class="control-label">Ratings Source?</label>
                                <div class="radio-inline">
                                    <label><input type="radio" name="ratings" @if ($user->ratings == 1) checked
                                        @endif value="1">IMDB</label>
                                </div>
                                <div class="radio-inline">
                                    <label><input type="radio" name="ratings" @if ($user->ratings == 0) checked
                                        @endif value="0">TMDB</label>
                                </div>
                            </div>
                            <div class="well some-padding">
                                <div class="text-center some-padding">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
