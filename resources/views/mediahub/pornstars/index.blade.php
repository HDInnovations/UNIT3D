
@extends('layout.default')

@section('title')
    <title>{{ __('mediahub.pornstars') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('mediahub.pornstars') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('mediahub.pornstars') }}
    </li>
@endsection

@section('page', 'page__pornstar--index')

@section('content')
    <div class="panelV2">
        <h1 class="panel__heading">{{ __('mediahub.pornstars') }}</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Aliases</th>
                    <th>Age</th>
                    <th>Country</th>
                    <th>Ethnicity</th>
                    <th>Scene Count</th>
                    <th>Links</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pornstars as $star)
                <tr>
                    <td>
                        @if (!empty($star->images))
                            <img src="{{ $star->images[0]['url'] ?? '' }}" alt="{{ $star->name }}" style="max-width:80px;max-height:80px;border-radius:8px;">
                        @endif
                    </td>
                    <td><a href="{{ route('mediahub.pornstars.show', $star->id) }}">{{ $star->name }}</a></td>
                    <td>{{ is_array($star->aliases) ? implode(', ', $star->aliases) : $star->aliases }}</td>
                    <td>{{ $star->age }}</td>
                    <td>{{ $star->country }}</td>
                    <td>{{ $star->ethnicity }}</td>
                    <td>{{ $star->scene_count }}</td>
                    <td>
                        @if (!empty($star->urls))
                            @foreach ($star->urls as $url)
                                <a href="{{ $url['url'] }}" target="_blank">{{ $url['type'] }}</a><br>
                            @endforeach
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
