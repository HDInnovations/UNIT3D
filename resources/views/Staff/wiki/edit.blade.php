@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.wiki_categories.index') }}" class="breadcrumb__link">Wikis</a>
    </li>
    <li class="breadcrumbV2">
        {{ $wiki->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-wiki--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.edit') }} wiki: {{ $wiki->name }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.wikis.update', ['wiki' => $wiki]) }}"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="name"
                        required
                        value="{{ $wiki->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="category_id" name="category_id" class="form__select">
                        <option value="{{ $wiki->category_id }}" selected>
                            {{ $wiki->category->name }} (Current)
                        </option>
                        @foreach ($wikiCategories as $wikiCategory)
                            <option value="{{ $wikiCategory->id }}">
                                {{ $wikiCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="category_id">
                        {{ __('common.category') }}
                    </label>
                </p>
                @livewire('bbcode-input', ['name' => 'content', 'label' => __('common.content'), 'required' => true, 'content' => $wiki->content])
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
