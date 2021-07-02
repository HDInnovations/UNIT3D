@extends('layout.default')

@section('title')
    <title>@lang('ticket.helpdesk') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('tickets.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('ticket.helpdesk')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('tickets.create') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('ticket.create-ticket')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-sm-10 col-md-offset-1">
                <div class="panel panel-chat shoutbox">
                    <div class="panel-heading"><i class="fas fa-plus"></i> @lang('ticket.create-ticket')</div>
                    <div class="panel-body">
                        @if(session('errors'))
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h6><b>@lang('ticket.fix-errors')</b></h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('tickets.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label for="category">@lang('ticket.category') <span class="text-danger small">*</span></label>
                                    <select name="category" id="category" class="form-control">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="priority">@lang('ticket.priority') <span class="text-danger small">*</span></label>
                                    <select name="priority" id="priority" class="form-control">
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-12">
                                    <label for="subject">@lang('ticket.subject') <span class="text-danger small">*</span></label>
                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="@lang('ticket.subject-enter')" autocomplete="off">
                                </div>
                                <div class="form-group col-12">
                                    <label for="body">@lang('ticket.body') <span class="text-danger small">*</span></label>
                                    <textarea name="body" id="body" cols="30" rows="3" class="form-control" placeholder="@lang('ticket.body-enter')"></textarea>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-md"><i class="fas fa-save"></i> @lang('ticket.submit-ticket')</button>
                                    <button type="reset" class="btn btn-danger btn-md"><i class="fas fa-eraser"></i> @lang('ticket.reset')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection