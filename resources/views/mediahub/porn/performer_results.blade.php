@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Performer Results for StashDB ID: {{ $id }}</h2>
    <h3>Scenes</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Release Date</th>
                <th>Studio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scenes as $scene)
                <tr>
                    <td>{{ $scene->title ?? 'N/A' }}</td>
                    <td>{{ $scene->release_date ?? 'N/A' }}</td>
                    <td>{{ $scene->studio->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h3>Torrents</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Size</th>
                <th>Added</th>
            </tr>
        </thead>
        <tbody>
            @foreach($torrents as $torrent)
                <tr>
                    <td>{{ $torrent->name ?? 'N/A' }}</td>
                    <td>{{ $torrent->size ?? 'N/A' }}</td>
                    <td>{{ $torrent->created_at ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
