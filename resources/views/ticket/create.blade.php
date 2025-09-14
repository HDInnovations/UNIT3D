@extends('layout.with-main')

@section('title')
    <title>{{ __('ticket.helpdesk') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('tickets.index') }}" class="breadcrumb__link">
            {{ __('ticket.helpdesk') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('ticket.create-ticket') }}
    </li>
@endsection

@section('page', 'page__ticket--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            <i class="fas fa-plus"></i>
            {{ __('ticket.create-ticket') }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                action="{{ route('tickets.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                <p class="form__group">
                    <select id="category_id" class="form__select" name="category_id" required>
                        <option hidden disabled selected value=""></option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <label for="category_id" class="form__label form__label--floating">
                        {{ __('ticket.category') }}
                    </label>
                </p>
                <p class="form__group">
                    <select name="priority_id" id="priority_id" class="form__select" required>
                        <option hidden disabled selected value=""></option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                        @endforeach
                    </select>
                    <label for="priority_id" class="form__label form__label--floating">
                        {{ __('ticket.priority') }}
                    </label>
                </p>
                <p class="form__group">
                    <input id="ticket_subject" class="form__text" name="subject" required />
                    <label for="ticket_subject" class="form__label form__label--floating">
                        {{ __('ticket.subject') }}
                    </label>
                </p>
                <p class="form__group">
                    <textarea id="body" class="form__textarea" name="body" required></textarea>
                    <label for="body" class="form__label form__label--floating">
                        {{ __('ticket.body') }}
                    </label>
                </p>
                <p class="form__group">
                    <label for="attachments" class="form__label">
                        {{ __('ticket.attachments') }}
                        <span class="text-danger small">
                            {{ __('ticket.attachment-limit') }}
                        </span>
                    </label>
                    <input
                        id="attachments"
                        type="file"
                        name="attachments[]"
                        class="upload-form-file form__file"
                        multiple
                    />
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
