@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumb--active">Pages</li>
@endsection

@section('page', 'page__page--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Pages</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td>
                                <a href="{{ route('pages.show', ['page' => $page]) }}">
                                    {{ $page->name }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
