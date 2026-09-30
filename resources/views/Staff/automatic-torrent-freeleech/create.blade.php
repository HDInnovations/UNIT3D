@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a
            href="{{ route('staff.automatic_torrent_freeleeches.index') }}"
            class="breadcrumb__link"
        >
            {{ __('vltava.staff.automatic_freeleeches') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-automatic-torrent-freeleech--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff-interface.automatic-torrent-freeleech-add-heading') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.automatic_torrent_freeleeches.store') }}"
            >
                @csrf
                <p class="form__group">
                    <input
                        type="text"
                        name="position"
                        id="position"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        required
                        value="{{ old('position') }}"
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="name_regex"
                        class="form__text"
                        name="name_regex"
                        placeholder=" "
                        type="text"
                        value="{{ old('name_regex') }}"
                    />
                    <label class="form__label form__label--floating" for="name_regex">
                        {{ __('vltava.staff.name_regex') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        type="text"
                        name="size"
                        id="size"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                        value="{{ old('size') }}"
                    />
                    <label class="form__label form__label--floating" for="size">
                        {{ __('staff-interface.minimum-torrent-size-bytes') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="category_id" name="category_id" class="form__select">
                        <option hidden selected disabled value="">{{ __('vltava.staff.any') }}</option>
                        @foreach ($categories as $category)
                            <option
                                class="form__option"
                                value="{{ $category->id }}"
                                @selected(old('category_id') == $category->id)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="category_id">
                        {{ __('torrent.category') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="type_id" name="type_id" class="form__select">
                        <option hidden disabled selected value="">{{ __('vltava.staff.any') }}</option>
                        @foreach ($types as $type)
                            <option
                                value="{{ $type->id }}"
                                @selected(old('type_id') == $type->id)
                            >
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="type_id">
                        {{ __('torrent.type') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="resolution_id" name="resolution_id" class="form__select">
                        <option hidden disabled selected value="">{{ __('vltava.staff.any') }}</option>
                        @foreach ($resolutions as $resolution)
                            <option
                                value="{{ $resolution->id }}"
                                @selected(old('resolution_id') == $resolution->id)
                            >
                                {{ $resolution->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="resolution_id">
                        {{ __('torrent.resolution') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        type="text"
                        name="freeleech_percentage"
                        id="freeleech_percentage"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]|[1-9][0-9]|100"
                        required
                        value="{{ old('freeleech_percentage') }}"
                    />
                    <label class="form__label form__label--floating" for="freeleech_percentage">
                        {{ __('vltava.staff.freeleech_percentage') }}
                    </label>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.add') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <div class="panel__body">
            {{ __('vltava.staff.automatic_freeleech_explanation') }}
        </div>
    </section>
@endsection
