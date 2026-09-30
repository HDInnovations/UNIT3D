@extends('layout.with-main')

@section('title')
    <title>{{ __('member-interface.wiki.wikis') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ config('other.title') }} - {{ __('member-interface.wiki.wikis') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">{{ __('member-interface.wiki.wikis') }}</li>
@endsection

@section('page', 'page__wiki--index')

@section('main')
    @foreach ($wiki_categories as $category)
        <section class="panelV2">
            <h2 class="panel__heading">
                {{ $category->name }}
            </h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <tbody>
                        @forelse ($category->wikis->sortBy('name') as $wiki)
                            <tr>
                                <td>
                                    <a href="{{ route('wikis.show', ['wiki' => $wiki]) }}">
                                        {{ $wiki->name }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>{{ __('member-interface.wiki.no-wikis-in-category') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
@endsection
