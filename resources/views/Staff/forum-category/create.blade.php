@extends('layout.with-main')

@section('title')
    <title>Add forums - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Add forums - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.forum_categories.index') }}" class="breadcrumb__link">
            Forum categories
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-forum-category--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Add a new forum category</h2>
        <div class="panel__body">
            <form class="form" method="POST" action="{{ route('staff.forum_categories.store') }}">
                @csrf
                <p class="form__group">
                    <input id="name" class="form__text" type="text" name="name" required />
                    <label class="form__label form__label--floating" for="name">Title</label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        inputmode="numeric"
                        name="position"
                        pattern="[0-9]*"
                        required
                        type="text"
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <textarea
                        id="description"
                        class="form__textarea"
                        name="description"
                        required
                    ></textarea>
                    <label class="form__label form__label--floating" for="description">
                        Description
                    </label>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">Save forum</button>
                </p>
            </form>
        </div>
    </section>
@endsection
