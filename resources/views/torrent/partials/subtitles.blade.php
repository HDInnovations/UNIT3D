<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">
            <i class="{{ config('other.font-awesome') }} fa-closed-captioning"></i>
            {{ __('common.subtitles') }}
        </h2>
        <div class="panel__actions">
            <div class="panel__action">
                <a
                    href="{{ route('subtitles.create', ['torrent_id' => $torrent->id]) }}"
                    class="form__button form__button--text"
                >
                    {{ __('common.add') }} {{ __('common.subtitle') }}
                </a>
            </div>
        </div>
    </header>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('common.language') }}</th>
                    <th>Note</th>
                    <th>{{ __('subtitle.extension') }}</th>
                    <th>{{ __('subtitle.size') }}</th>
                    <th>{{ __('subtitle.downloads') }}</th>
                    <th>{{ __('subtitle.uploaded') }}</th>
                    <th>{{ __('subtitle.uploader') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($torrent->subtitles as $subtitle)
                    <tr>
                        <td>{{ $subtitle->language->name }}</td>
                        <td>{{ $subtitle->note }}</td>
                        <td>{{ $subtitle->extension }}</td>
                        <td>{{ $subtitle->getSize() }}</td>
                        <td>{{ $subtitle->downloads }}</td>
                        <td>
                            <time
                                datetime="{{ $subtitle->created_at }}"
                                title="{{ $subtitle->created_at }}"
                            >
                                {{ $subtitle->created_at->diffForHumans() }}
                            </time>
                        </td>
                        <td>
                            <x-user-tag :user="$subtitle->user" :anon="$subtitle->anon" />
                        </td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    <a
                                        href="{{ route('subtitles.download', ['subtitle' => $subtitle]) }}"
                                        class="form__button form__button--text"
                                        title="{{ __('common.download') }}"
                                    >
                                        {{ __('common.download') }}
                                    </a>
                                </li>
                                @if (auth()->user()->group->is_modo || auth()->id() == $subtitle->user_id)
                                    <li class="data-table__action">
                                        <span x-data="dialog">
                                            <button
                                                class="form__button form__button--text"
                                                title="{{ __('common.edit') }}"
                                                x-bind="showDialog"
                                            >
                                                {{ __('common.edit') }}
                                            </button>
                                            <dialog class="dialog" x-bind="dialogElement">
                                                <h4 class="dialog__heading">
                                                    {{ __('common.edit') }}
                                                    {{ __('common.subtitle') }}
                                                </h4>
                                                <form
                                                    class="dialog__form"
                                                    method="POST"
                                                    action="{{ route('subtitles.update', ['subtitle' => $subtitle]) }}"
                                                    x-bind="dialogForm"
                                                >
                                                    @csrf
                                                    @method('PATCH')
                                                    <input
                                                        id="torrent_id"
                                                        name="torrent_id"
                                                        type="hidden"
                                                        value="{{ $torrent->id }}"
                                                    />
                                                    <p class="form__group">
                                                        <select
                                                            class="form__select"
                                                            id="language_id"
                                                            name="language_id"
                                                            required
                                                        >
                                                            <option
                                                                value="{{ $subtitle->language_id }}"
                                                                selected
                                                            >
                                                                {{ $subtitle->language->name }}
                                                                ({{ __('torrent.current') }})
                                                            </option>
                                                            @foreach (App\Models\MediaLanguage::query()->orderBy('name')->get() as $media_language)
                                                                <option
                                                                    value="{{ $media_language->id }}"
                                                                >
                                                                    {{ $media_language->name }}
                                                                    ({{ $media_language->code }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label
                                                            class="form__label form__label--floating"
                                                            for="language_id"
                                                        >
                                                            {{ __('common.language') }}
                                                        </label>
                                                    </p>
                                                    <p class="form__group">
                                                        <input
                                                            id="note"
                                                            class="form__text"
                                                            name="note"
                                                            type="text"
                                                            value="{{ $subtitle->note }}"
                                                            required
                                                        />
                                                        <label
                                                            class="form__label form__label--floating"
                                                            for="note"
                                                        >
                                                            {{ __('subtitle.note') }}
                                                        </label>
                                                    </p>
                                                    <p class="form__group">
                                                        <input
                                                            type="hidden"
                                                            name="anon"
                                                            value="0"
                                                        />
                                                        <input
                                                            id="anon"
                                                            class="form__checkbox"
                                                            name="anon"
                                                            type="checkbox"
                                                            value="1"
                                                            @checked($subtitle->anon)
                                                        />
                                                        <label class="form__label" for="anon">
                                                            {{ __('common.anonymous') }}?
                                                        </label>
                                                    </p>
                                                    <p class="form__group">
                                                        <button
                                                            class="form__button form__button--filled"
                                                        >
                                                            {{ __('common.save') }}
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
                                        </span>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            method="POST"
                                            action="{{ route('subtitles.destroy', ['subtitle' => $subtitle]) }}"
                                            x-data="confirmation"
                                            style="display: inline"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input
                                                id="torrent_id"
                                                name="torrent_id"
                                                type="hidden"
                                                value="{{ $torrent->id }}"
                                            />
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this subtitle: ' . $subtitle->language->name . '?') }}"
                                                class="form__button form__button--text"
                                            >
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No external subtitles available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
