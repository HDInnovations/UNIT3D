@extends('layout.default')

@section('breadcrumb')
    <li>
        <a href="{{ route('staff.dashboard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.staff-dashboard')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff.pages.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.pages')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container box">
        <h2>@lang('staff.pages')</h2>
        <a href="{{ route('staff.pages.create') }}" class="btn btn-primary">
            @lang('common.add')
            @lang(trans_choice('common.a-an-art',false))
            @lang('common.new-adj')
            @lang('staff.page')
        </a>

        <div class="table-responsive">
            <table class="table table-condensed table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>@lang('common.title')</th>
                        <th>@lang('common.date')</th>
                        <th>@lang('common.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td>
                                <a href="{{ route('pages.show', ['id' => $page->id]) }}">
                                    {{ $page->name }}
                                </a>
                            </td>
                            <td>
                                {{ $page->created_at }} ({{ $page->created_at->diffForHumans() }})
                            </td>
                            <td>
                                <form action="{{ route('staff.pages.destroy', ['id' => $page->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{ route('staff.pages.edit', ['id' => $page->id]) }}"
                                        class="btn btn-warning">@lang('common.edit')</a>
                                    <button type="submit" class="btn btn-danger">@lang('common.delete')</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
