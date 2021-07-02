@extends('layout.default')

@section('title')
    <title>{{ $season->name }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ $season->name }}">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('mediahub.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">MediaHub</span>
        </a>
    </li>
    <li>
        <a href="{{ route('mediahub.shows.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">TV Shows</span>
        </a>
    </li>
    <li>
        <a href="{{ route('mediahub.shows.show', ['id' => $show->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $show->name }}</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('mediahub.season.show', ['id' => $season->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $season->name }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            <section class="header">
                <div class="gradient movie">
                    <div class="inner_content">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1414 300">
                                <path class="cls-1" d="M531.37,223.71a15.76,15.76,0,0,0,3.15-22.07h0a15.76,15.76,0,0,0-22.07-3.15l-38.92,29.2a15.74,15.74,0,0,1-18.67-24.88L541,138.15a15.76,15.76,0,0,0-18.92-25.21L272.84,300H440.4A15.77,15.77,0,0,1,445,288.6l-.34.2Z"></path>
                                <path class="cls-1" d="M801.29,216.67l-1.08.81-.83.68c-29.5,23.63-64.11,48.17-64.11,48.17v-.09A15.74,15.74,0,0,1,713.62,244l.12-.16.44-.57c.1-.12.19-.25.29-.38l.06,0a15.9,15.9,0,0,1,2.81-2.52c6-4.59,38.89-29.9,52-38.67l.08-.05a15.75,15.75,0,0,0-19.85-24.44L609.2,282.51c-6.76,3.69-16,2.2-20.74-4.14a15.74,15.74,0,0,1,.61-19.64L676.68,193a15.76,15.76,0,0,0-18.92-25.21L481.54,300H683.06l-.06.06h11.06l.12-.06h48.59l77.44-58.11a15.76,15.76,0,1,0-18.92-25.21Z"></path>
                                <path class="cls-1" d="M563.68,104.48a15.69,15.69,0,0,0,25.1-18.84h0a15.69,15.69,0,0,0-25.1,18.84Z"></path><path class="cls-1" d="M1091.43.46a15.77,15.77,0,0,1-4.58,11.4l.34-.2-86.74,65.09a15.76,15.76,0,0,0-3.15,22.07h0a15.76,15.76,0,0,0,22.07,3.15l38.92-29.2A15.74,15.74,0,0,1,1077,97.64l-86.17,64.66a15.76,15.76,0,0,0,18.92,25.21L1259,.46Z"></path>
                                <path class="cls-1" d="M848.78.46l.06-.06H837.77l-.12.06H789.06L711.62,58.57a15.76,15.76,0,1,0,18.92,25.21l1.08-.81.83-.68c29.5-23.62,64.11-48.17,64.11-48.17v.09a15.74,15.74,0,0,1,21.65,22.21l-.12.16-.44.57c-.1.12-.19.25-.29.38l-.06,0a15.9,15.9,0,0,1-2.81,2.52c-6,4.59-38.89,29.9-52,38.67l-.08.05a15.75,15.75,0,0,0,19.85,24.44L922.63,18c6.76-3.69,16-2.2,20.74,4.14a15.74,15.74,0,0,1-.61,19.64l-87.61,65.74a15.76,15.76,0,0,0,18.92,25.21L1050.29.46Z"></path>
                                <path class="cls-1" d="M968.15,196A15.69,15.69,0,0,0,943,214.83h0A15.69,15.69,0,0,0,968.15,196Z"></path>
                            </svg>
                        </div>
                        <img src="{{ isset($season->poster) ? \tmdb_image('poster_small', $show->poster) : 'https://via.placeholder.com/80x120' }}" style="width: 80px; border-radius: 5px; z-index: 1; position: relative; margin-right: 40px;">
                        <a href="{{ route('mediahub.shows.show', ['id' => $show->id]) }}">
                            <h2>{{ $season->name }} ({{ $season->air_date }})</h2>
                            <h3 style="z-index: 1; position: relative; font-size:20px; margin: 0; text-decoration: underline ;">
                                ← Back to {{ $show->name }} season list
                            </h3>
                        </a>
                    </div>
                </div>
            </section>

            <div>
                @foreach($season->episodes as $episode)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card is-torrent" style="margin-top: 0; margin-bottom: 20px; height: auto;">
                                <div class="card_head">
                                    <span class="badge-user text-bold" style="float:right;">
                                        Episode {{ $episode->episode_number }}
                                    </span>
                                </div>
                                <div class="card_body">
                                    <div class="body_poster">
                                        <img src="{{ isset($episode->still) ? \tmdb_image('still_mid', $episode->still) : 'https://via.placeholder.com/400x225' }}" class="show-poster">
                                    </div>
                                    <div class="body_description" style=" height: 190px;">
                                        <h3 class="description_title">
                                            {{ $episode->name }} -
                                                @if($episode->air_date)
                                                    <span class="text-bold text-pink"> {{ $episode->air_date }}</span>
                                                @endif
                                        </h3>
                                        <p class="description_plot">
                                            {{ $episode->overview }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection