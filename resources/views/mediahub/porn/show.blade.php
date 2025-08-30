@extends('layout.with-main')

@section('title', $scene->title . ' - Porn Scene - MediaHub')

@section('main')
    <h1>{{ $scene->title }}</h1>
    <div><strong>Studio:</strong> {{ $scene->studio_name }}</div>
    <div><strong>Release Date:</strong> {{ $scene->release_date }}</div>
    <div><strong>Duration:</strong> {{ $scene->duration }} seconds</div>
    <div><strong>Performers:</strong> {{ collect($scene->performers)->pluck('performer.name')->join(', ') }}</div>
    <div><strong>Tags:</strong> {{ collect($scene->tags)->pluck('name')->join(', ') }}</div>
    <div><strong>Details:</strong> {{ $scene->details }}</div>
    <div><strong>Fingerprints:</strong>
        <ul>
            @foreach ($scene->fingerprints as $fp)
                <li>{{ $fp['algorithm'] }}: {{ $fp['hash'] }} (Reports: {{ $fp['reports'] }})</li>
            @endforeach
        </ul>
    </div>
    <div><strong>URLs:</strong>
        <ul>
            @foreach ($scene->urls as $url)
                <li>{{ $url['type'] }}: <a href="{{ $url['url'] }}" target="_blank">{{ $url['url'] }}</a></li>
            @endforeach
        </ul>
    </div>
@endsection
