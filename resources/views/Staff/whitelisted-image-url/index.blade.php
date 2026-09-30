@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('staff-interface.whitelisted-image-urls') }}</li>
@endsection

@section('page', 'page__staff-whitelisted-image-url--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('staff-interface.whitelisted-image-urls') }}</h2>
            <div class="panel__actions">
                <div class="panel__action" x-data="dialog">
                    <button class="form__button form__button--text" x-bind="showDialog">
                        {{ __('common.add') }}
                    </button>
                    <dialog class="dialog" x-bind="dialogElement">
                        <h3 class="dialog__heading">{{ __('common.add') }}</h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('staff.whitelisted_image_urls.store') }}"
                            x-bind="dialogForm"
                        >
                            @csrf
                            <p class="form__group">
                                <input
                                    id="pattern"
                                    class="form__text"
                                    name="pattern"
                                    placeholder=" "
                                    required
                                    type="text"
                                />
                                <label class="form__label form__label--floating" for="pattern">
                                    {{ __('staff-interface.url-pattern') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('common.add') }}
                                </button>
                                <button
                                    formmethod="dialog"
                                    formnovalidate
                                    class="form__button form__button--outlined"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <th>{{ __('staff-interface.id') }}</th>
                    <th>{{ __('staff-interface.url-pattern') }}</th>
                    <th>{{ __('staff-interface.example-bypassed-url') }}</th>
                    <th>{{ __('common.created_at') }}</th>
                    <th>{{ __('forum.updated-at') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </thead>
                <tbody>
                    @forelse ($whitelistedImageUrls as $whitelistedImageUrl)
                        <tr>
                            <td>{{ $whitelistedImageUrl->id }}</td>
                            <td>{{ $whitelistedImageUrl->pattern }}</td>
                            <td>
                                {{ str_replace(['**', '*'], ['my.evil.example/evil', '_evil_'], $whitelistedImageUrl->pattern) }}
                            </td>
                            <td>
                                <time
                                    datetime="{{ $whitelistedImageUrl->created_at }}"
                                    title="{{ $whitelistedImageUrl->created_at }}"
                                >
                                    {{ $whitelistedImageUrl->created_at }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $whitelistedImageUrl->updated_at }}"
                                    title="{{ $whitelistedImageUrl->updated_at }}"
                                >
                                    {{ $whitelistedImageUrl->updated_at }}
                                </time>
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action" x-data="dialog">
                                        <button
                                            class="form__button form__button--text"
                                            x-bind="showDialog"
                                        >
                                            {{ __('common.edit') }}
                                        </button>
                                        <dialog class="dialog" x-bind="dialogElement">
                                            <h3 class="dialog__heading">
                                                {{ __('common.edit') }}
                                            </h3>
                                            <form
                                                class="dialog__form"
                                                method="POST"
                                                action="{{ route('staff.whitelisted_image_urls.update', ['whitelistedImageUrl' => $whitelistedImageUrl]) }}"
                                                x-bind="dialogForm"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <p class="form__group">
                                                    <input
                                                        id="pattern"
                                                        class="form__text"
                                                        name="pattern"
                                                        placeholder=" "
                                                        required
                                                        type="text"
                                                        value="{{ $whitelistedImageUrl->pattern }}"
                                                    />
                                                    <label
                                                        class="form__label form__label--floating"
                                                        for="pattern"
                                                    >
                                                        {{ __('staff-interface.url-pattern') }}
                                                    </label>
                                                </p>
                                                <p class="form__group">
                                                    <button
                                                        class="form__button form__button--filled"
                                                    >
                                                        {{ __('common.edit') }}
                                                    </button>
                                                    <button
                                                        formmethod="dialog"
                                                        formnovalidate
                                                        class="form__button form__button--outlined"
                                                    >
                                                        {{ __('common.cancel') }}
                                                    </button>
                                                </p>
                                            </form>
                                        </dialog>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.whitelisted_image_urls.destroy', ['whitelistedImageUrl' => $whitelistedImageUrl]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                class="form__button form__button--text"
                                                data-b64-deletion-message="{{ base64_encode(__('staff-interface.delete-whitelisted-image-url-confirmation', ['pattern' => $whitelistedImageUrl->pattern])) }}"
                                            >
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('staff-interface.no-whitelisted-image-urls') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <div class="panel__body">
            <p>
                {{ __('staff-interface.image-proxy-explanation') }}
            </p>
            <p>
                {{ __('staff-interface.image-whitelist-exception-explanation') }}
            </p>
            <p>
                {!! __('staff-interface.image-whitelist-wildcard-explanation', [
                    'star' => '<code>*</code>',
                    'slash' => '<code>/</code>',
                    'dot' => '<code>.</code>',
                    'double_star' => '<code>**</code>',
                    'url' => '<code>https://evil.example/subdomain.whitelisted-domain.example/image.png</code>',
                ]) !!}
            </p>
            <p>
                {!! __('staff-interface.image-whitelist-subdomain-explanation', [
                    'dot' => '<code>.</code>',
                    'bad_example' => '<code>https://evilimgur.com</code>',
                    'bad_pattern' => '<code>https://*imgur.com/**</code>',
                    'good_pattern' => '<code>https://*.imgur.com/**</code>',
                    'best_pattern' => '<code>https://i.imgur.com/**</code>',
                ]) !!}
            </p>
        </div>
    </section>
@endsection
