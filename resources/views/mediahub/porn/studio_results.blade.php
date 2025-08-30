@extends('layout.with-main')

@section('title', 'Studio Results')

@section('main')
    <h1>Studio Results for ID: {{ $id }}</h1>
    <h2>Scenes</h2>
    @if($scenes->count())
        <ul>
            @foreach($scenes as $scene)
                <li>{{ $scene->title ?? 'Untitled' }} ({{ $scene->id }})</li>
            @endforeach
        </ul>
    @else
        <p>No scenes found for this studio.</p>
    @endif
    <h2>Torrents</h2>
    @if($torrents->count())
        <ul>
            @foreach($torrents as $torrent)
                <li>{{ $torrent->name ?? 'Unnamed Torrent' }} ({{ $torrent->id }})</li>
            @endforeach
        </ul>
    @else
        <p>No torrents found for this studio.</p>
    @endif
@endsection
