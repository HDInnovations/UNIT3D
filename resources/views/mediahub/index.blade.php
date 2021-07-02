@extends('layout.default')

@section('title')
    <title>@lang('mediahub.title') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="MediaHub">
@endsection

@section('breadcrumb')
    <li class="active">
        <a href="{{ route('mediahub.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('mediahub.title')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            <div class="header mediahub text-center">
                <div class="mediahub_content">
                    <div class="content">

                        <h1 style=" height: 150px; font-family: Shrikhand, cursive; font-size: 7em; font-weight: 400; margin: 0;">@lang('mediahub.title')</h1>
                        <h3>@lang('mediahub.select-hub')</h3>

                        <div class="blocks text-center" style=" justify-content: center;">
                            {{--TV Shows--}}
                            <a href="{{ route('mediahub.shows.index') }}" class="">
                                <div class="movie media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
                                    <h2>@lang('mediahub.shows') Hub</h2>
                                    <span style="background-color: #01d277;"></span>
                                    <h2 style="font-size: 12px;">{{ $tv }} @lang('mediahub.shows')</h2>
                                </div>
                            </a>

                            {{--Movies--}}
                            <a href="{{ route('mediahub.movies.index') }}" class="">
                                <div class="movie media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
                                    <h2>@lang('mediahub.movies') Hub</h2>
                                    <span style="background-color: #01d277;"></span>
                                    <h2 style="font-size: 12px;">{{ $movies }} @lang('mediahub.movies')</h2>
                                </div>
                            </a>

                            {{--Collections--}}
                            <a href="{{ route('mediahub.collections.index') }}" class="">
                                <div class="movie media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
                                    <h2>@lang('mediahub.collections') Hub</h2>
                                    <span style="background-color: #01d277;"></span>
                                    <h2 style="font-size: 12px;">{{ $collections }} @lang('mediahub.collections')</h2>
                                </div>
                            </a>

                            {{--People--}}
                            <a href="{{ route('mediahub.persons.index') }}" class="">
                                <div class="movie media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
                                    <h2>@lang('mediahub.persons') Hub</h2>
                                    <span style="background-color: #01d277;"></span>
                                    <h2 style="font-size: 12px;">{{ $persons }} @lang('mediahub.persons')</h2>
                                </div>
                            </a>

                            {{--Genres--}}
                            <a href="{{ route('mediahub.genres.index') }}" class="">
                                <div class="movie media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
                                    <h2>@lang('mediahub.genres') Hub</h2>
                                    <span style="background-color: #01d277;"></span>
                                    <h2 style="font-size: 12px;">{{ $genres }} @lang('mediahub.genres')</h2>
                                </div>
                            </a>

                            {{--Networks--}}
                            <a href="{{ route('mediahub.networks.index') }}" class="">
                                <div class="movie media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
                                    <h2>@lang('mediahub.networks') Hub</h2>
                                    <span style="background-color: #01d277;"></span>
                                    <h2 style="font-size: 12px;">{{ $networks }} @lang('mediahub.networks')</h2>
                                </div>
                            </a>

                            {{--Companies--}}
                            <a href="{{ route('mediahub.companies.index') }}" class="">
                                <div class="movie media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
                                    <h2>@lang('mediahub.companies') Hub</h2>
                                    <span style="background-color: #01d277;"></span>
                                    <h2 style="font-size: 12px;">{{ $companies }} @lang('mediahub.companies')</h2>
                                </div>
                            </a>
                        </div>
                        <br>
                        <h6 style="font-size: 10px;">
                            <i>@lang('mediahub.disclaimer')</i>
                        </h6>
                        <img class="mb-20" src="/img/tmdb_long.svg" style="width: 200px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
