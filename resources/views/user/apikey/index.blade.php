@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ $user->username }} - Security - {{ __('common.members') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a
            href="{{ route('users.general_settings.edit', ['user' => $user]) }}"
            class="breadcrumb__link"
        >
            {{ __('user.settings') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.apikeys') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-apikey--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Active {{ __('user.apikeys') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <button class="form__button form__button--text" popovertarget="apikey-add">
                        {{ __('common.create') }}
                    </button>
                    <dialog id="apikey-add" class="dialog" popover>
                        <h3 class="dialog__heading">{{ __('common.create') }}</h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('users.apikeys.store', ['user' => $user]) }}"
                        >
                            @csrf
                            <p class="form__group">
                                <input
                                    id="name"
                                    class="form__text"
                                    name="name"
                                    type="text"
                                    required
                                />
                                <label class="form__label form__label--floating" for="name">
                                    {{ __('common.name') }}
                                </label>
                                <span class="form__hint">The name of the app using the key</span>
                            </p>
                            <p class="form__group">
                                <input
                                    id="expires_at"
                                    class="form__text"
                                    name="expires_at"
                                    type="date"
                                    min="{{ today()->format('Y-m-d') }}"
                                    max="{{ today()->addYear()->format('Y-m-d') }}"
                                    required
                                />
                                <label class="form__label form__label--floating" for="expires_at">
                                    {{ __('user.expires-on') }}
                                </label>
                            </p>
                            <fieldset class="form form__fieldset">
                                <legend>Scopes</legend>
                                <p class="form__group">
                                    Do not grant more permissions than necessary.
                                </p>
                                <p class="form__group">
                                    <input type="hidden" name="can_search" value="0" />
                                    <input
                                        id="can_search"
                                        class="form__checkbox"
                                        name="can_search"
                                        value="1"
                                        type="checkbox"
                                    />
                                    <label class="form__label" for="can_search">
                                        {{ __('common.search') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <input type="hidden" name="can_download" value="0" />
                                    <input
                                        id="can_download"
                                        class="form__checkbox"
                                        name="can_download"
                                        value="1"
                                        type="checkbox"
                                    />
                                    <label class="form__label" for="can_download">
                                        {{ __('common.download') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <input type="hidden" name="can_upload" value="0" />
                                    <input
                                        id="can_upload"
                                        class="form__checkbox"
                                        name="can_upload"
                                        value="1"
                                        type="checkbox"
                                    />
                                    <label class="form__label" for="can_upload">
                                        {{ __('common.upload') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <input type="hidden" name="can_view_user" value="0" />
                                    <input
                                        id="can_view_user"
                                        class="form__checkbox"
                                        name="can_view_user"
                                        value="1"
                                        type="checkbox"
                                    />
                                    <label class="form__label" for="can_view_user">
                                        {{ __('common.user') }}
                                    </label>
                                </p>
                            </fieldset>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('common.create') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="apikey-add"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                    @if (Session::has('apikey'))
                        <dialog
                            class="dialog"
                            popover
                            x-data="newApikey"
                            x-init="$el.showModal()"
                        >
                            <h3 class="dialog__heading">New key</h3>
                            <div class="dialog__form">
                                <p>
                                    Your new API key has been generated. This is the only time the
                                    full key will be visible.
                                </p>
                                <pre><code style="overflow-wrap: break-word" x-ref="apikey">{{ Session::get('apikey') }}</code></pre>
                                <p class="form__group">
                                    <button
                                        class="form__button form__button--outlined"
                                        x-on:click="$root.close()"
                                    >
                                        Understood
                                    </button>
                                    <button
                                        class="form__button form__button--outlined"
                                        x-on:click.stop="copy"
                                    >
                                        Copy key to clipboard
                                    </button>
                                </p>
                            </div>
                            <script
                                nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}"
                            >
                                document.addEventListener('alpine:init', () => {
                                    Alpine.data('newApikey', () => ({
                                        copy() {
                                            navigator.clipboard.writeText(
                                                this.$refs.apikey.textContent,
                                            );
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                showConfirmButton: false,
                                                timer: 3000,
                                                icon: 'success',
                                                title: 'Copied to clipboard!',
                                            });
                                        },
                                    }));
                                });
                            </script>
                        </dialog>
                    @endif
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('user.apikey') }}</th>
                        <th>{{ __('common.created_at') }}</th>
                        <th>Last used</th>
                        <th>{{ __('user.expires-on') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($apikeys as $apikey)
                        <tr>
                            <td>{{ $apikey->name }}</td>
                            <td>
                                {{ Str::take($apikey->content, 3) }}&hellip;{{ Str::take($apikey->content, -4) }}
                            </td>
                            <td>
                                <time
                                    datetime="{{ $apikey->created_at }}"
                                    title="{{ $apikey->created_at }}"
                                >
                                    {{ $apikey->created_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                @if ($apikey->last_used_at === null)
                                    N/A
                                @else
                                    <time
                                        datetime="{{ $apikey->last_used_at }}"
                                        title="{{ $apikey->last_used_at }}"
                                    >
                                        {{ $apikey->last_used_at }}
                                    </time>
                                @endif
                            </td>
                            <td>
                                <time
                                    datetime="{{ $apikey->expires_at }}"
                                    title="{{ $apikey->expires_at }}"
                                >
                                    {{ $apikey->expires_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('users.apikeys.destroy', ['user' => $user, 'apikey' => $apikey]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this apikey: ' . $apikey->name) }}"
                                                class="form__button form__button--text"
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
                            <td colspan="6">No apikey history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">Deleted {{ __('user.apikeys') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('user.apikey') }}</th>
                        <th>{{ __('common.created_at') }}</th>
                        <th>Last used</th>
                        <th>{{ __('user.deleted-on') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($deletedApikeys as $apikey)
                        <tr>
                            <td>{{ $apikey->name }}</td>
                            <td>
                                {{ Str::take($apikey->content, 3) }}&hellip;{{ Str::take($apikey->content, -4) }}
                            </td>
                            <td>
                                <time
                                    datetime="{{ $apikey->created_at }}"
                                    title="{{ $apikey->created_at }}"
                                >
                                    {{ $apikey->created_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                @if ($apikey->last_used_at === null)
                                    N/A
                                @else
                                    <time
                                        datetime="{{ $apikey->last_used_at }}"
                                        title="{{ $apikey->last_used_at }}"
                                    >
                                        {{ $apikey->last_used_at }}
                                    </time>
                                @endif
                            </td>
                            <td>
                                <time
                                    datetime="{{ $apikey->deleted_at }}"
                                    title="{{ $apikey->deleted_at }}"
                                >
                                    @if ($apikey->deleted_at === null)
                                        <i
                                            class="{{ config('other.font-awesome') }} fa-check text-green"
                                        ></i>
                                        Currently in use
                                    @else
                                        {{ $apikey->deleted_at->format('Y-m-d') }}
                                    @endif
                                </time>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No apikey history</td>
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
            <p>Use a separate apikey for each application you provide it to.</p>
            <p>{{ __('user.reset-api-help') }}.</p>
        </div>
    </section>
@endsection
