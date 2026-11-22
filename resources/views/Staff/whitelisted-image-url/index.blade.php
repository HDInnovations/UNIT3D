@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Whitelisted image URLs</li>
@endsection

@section('page', 'page__staff-whitelisted-image-url--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Whitelisted image URLs</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <button class="form__button form__button--text" popovertarget="url-add">
                        {{ __('common.add') }}
                    </button>
                    <dialog id="url-add" class="dialog" popover>
                        <h3 class="dialog__heading">{{ __('common.add') }}</h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('staff.whitelisted_image_urls.store') }}"
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
                                    URL pattern
                                </label>
                            </p>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('common.add') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="url-add"
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
                    <th>ID</th>
                    <th>URL pattern</th>
                    <th>Example bypassed URL</th>
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
                                    <li class="data-table__action">
                                        <button
                                            class="form__button form__button--text"
                                            popovertarget="url-edit-{{ $whitelistedImageUrl->id }}"
                                        >
                                            {{ __('common.edit') }}
                                        </button>
                                        <dialog
                                            id="url-edit-{{ $whitelistedImageUrl->id }}"
                                            class="dialog"
                                            popover
                                        >
                                            <h3 class="dialog__heading">
                                                {{ __('common.edit') }}
                                            </h3>
                                            <form
                                                class="dialog__form"
                                                method="POST"
                                                action="{{ route('staff.whitelisted_image_urls.update', ['whitelistedImageUrl' => $whitelistedImageUrl]) }}"
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
                                                        URL pattern
                                                    </label>
                                                </p>
                                                <p class="form__group">
                                                    <button
                                                        class="form__button form__button--filled"
                                                    >
                                                        {{ __('common.edit') }}
                                                    </button>
                                                    <button
                                                        class="form__button form__button--outlined"
                                                        type="button"
                                                        popovertarget="url-edit-{{ $whitelistedImageUrl->id }}"
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
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to remove this whitelisted image url: ' . $whitelistedImageUrl->pattern . '?') }}"
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
                            <td colspan="5">No whitelisted image urls.</td>
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
                When users add images via BBCode, other users will load the image on page load. This
                means whoever operates the website of the image URL can view the connecting IPs.
                Therefore, all images entered via BBCode are proxied.
            </p>
            <p>
                In exception cases where the proxy blocks a popular image host, that image URL
                should be whitelisted here. This will bypass the proxy and directly link the image.
                Any trusted image URLs can also be included here to increase client image loading
                speeds.
            </p>
            <p>
                You can use
                <code>*</code>
                as a wildcard when matching URLs. A
                <code>*</code>
                wildcard will match everything except for
                <code>/</code>
                and
                <code>.</code>
                in the URL. You can also use
                <code>**</code>
                to match any character. You must never use
                <code>**</code>
                for matching subdomains as any user can register their own domain and link
                <code>https://evil.example/subdomain.whitelisted-domain.example/image.png</code>
                to bypass the proxy.
            </p>
            <p>
                To match a url with a variable subdomain, make sure to manually specify the
                <code>.</code>
                otherwise a user can register
                <code>https://evilimgur.com</code>
                if you use
                <code>https://*imgur.com/**</code>
                (bad) instead of
                <code>https://*.imgur.com/**</code>
                (good) or
                <code>https://i.imgur.com/**</code>
                (best).
            </p>
        </div>
    </section>
@endsection
