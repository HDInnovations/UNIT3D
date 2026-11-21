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
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-wiki--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.add') }}
            {{ trans_choice('common.a-an-art', false) }}
            {{ __('common.new-adj') }}
            Wiki
        </h2>
        <div class="panel__body">
            <form class="form" method="POST" action="{{ route('staff.wikis.store') }}">
                @csrf
                <p class="form__group">
                    <input id="name" class="form__text" type="text" name="name" required />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="category_id"
                        name="category_id"
                        class="form__select"
                        x-data="{ selected: {{ Js::from($wikiCategoryId) }} || '' }"
                        x-model="selected"
                        x-bind:class="selected === '' ? 'form__selected--default' : ''"
                        required
                    >
                        <option disabled hidden></option>
                        @foreach ($wikiCategories as $wikiCategory)
                            <option
                                value="{{ $wikiCategory->id }}"
                                @selected($wikiCategory->id === $wikiCategoryId)
                            >
                                {{ $wikiCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="category_id">
                        {{ __('common.category') }}
                    </label>
                </p>
                @livewire('bbcode-input', ['name' => 'content', 'label' => __('common.content'), 'required' => true])
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
