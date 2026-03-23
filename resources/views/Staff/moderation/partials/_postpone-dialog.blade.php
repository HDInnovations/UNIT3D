<li class="data-table__action" x-data="dialog">
    <button class="form__button form__button--filled" x-bind="showDialog">
        <i class="{{ config('other.font-awesome') }} fa-pause"></i>
        {{ __('common.moderation-postpone') }}
    </button>
    <dialog class="dialog" x-bind="dialogElement">
        <h4 class="dialog__heading">
            {{ __('common.moderation-postpone') }} {{ __('torrent.torrent') }}: {{ $torrent->name }}
        </h4>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('staff.moderation.update', ['id' => $torrent->id]) }}"
            x-bind="dialogForm"
        >
            @csrf
            <input type="hidden" name="type" value="{{ __('torrent.torrent') }}" />
            <input type="hidden" name="id" value="{{ $torrent->id }}" />
            <input type="hidden" name="old_status" value="{{ $torrent->status }}" />
            <input
                type="hidden"
                name="status"
                value="{{ \App\Enums\ModerationStatus::POSTPONED }}"
            />
            <p class="form__group">
                <textarea
                    class="form__textarea"
                    name="message"
                    id="message"
                    >{{ old('message') }}</textarea
                >
                <label class="form__label form__label--floating" for="message">
                    Postpone message
                </label>
            </p>
            <p class="form__group">
                <button class="form__button form__button--filled">
                    {{ __('common.moderation-postpone') }}
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
