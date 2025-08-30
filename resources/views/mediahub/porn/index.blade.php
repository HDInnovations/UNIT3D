@extends('layout.with-main')

@section('title', 'Porn Scenes - MediaHub')

@section('main')
    <h1>StashDB Scenes</h1>
    <div class="scene-list">
        @foreach ($scenes as $scene)
            <div class="scene-item">
                <h2><a href="{{ route('mediahub.porn.show', $scene->id) }}">{{ $scene->title }}</a></h2>
                <div><strong>StashDB Scene ID:</strong> {{ $scene->id }}</div>
                <div><strong>Studio:</strong> {{ $scene->studio_name }}</div>
                <div><strong>Release Date:</strong> {{ $scene->release_date }}</div>
                <div><strong>Performers:</strong> {{ collect($scene->performers)->pluck('performer.name')->join(', ') }}</div>
                <div><strong>Tags:</strong> {{ collect($scene->tags)->pluck('name')->join(', ') }}</div>
                <a href="{{ route('mediahub.porn.gridByStashId', $scene->id) }}" class="btn btn-sm btn-info">View Grid</a>
            </div>
        @endforeach
    </div>
    {{ $scenes->links() }}
@endsection
