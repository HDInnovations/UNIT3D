@extends('layout.default')

@section('content')
    <article class="sidebar2">
        <div>
            @yield('main')
        </div>
        <aside>
            @yield('sidebar')
        </aside>
    </article>
@endsection
