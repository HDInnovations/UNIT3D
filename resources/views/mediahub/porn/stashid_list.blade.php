@extends('layout.with-main')

@section('title', 'Known StashDB Scene IDs')

@section('main')
    <h1>Known StashDB Scene IDs</h1>
    <ul>
        @foreach ($stashids as $stashid)
            <li>
                <a href="{{ route('mediahub.porn.torrentsByStashId', $stashid) }}">StashDB Scene #{{ $stashid }}</a>
            </li>
        @endforeach
    </ul>
@endsection
