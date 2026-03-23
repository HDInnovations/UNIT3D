<li class="data-table__action" x-data="dialog">
    <button class="form__button form__button--filled" x-bind="showDialog">
        <i class="{{ config('other.font-awesome') }} fa-thumbs-down"></i>
        {{ __('common.moderation-reject') }}
    </button>
    <dialog class="dialog" x-bind="dialogElement">
        <h3 class="dialog__heading">
            {{ __('common.moderation-reject') }} {{ __('torrent.torrent') }}: {{ $torrent->name }}
        </h3>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('staff.moderation.update', ['id' => $torrent->id]) }}"
            x-bind="dialogForm"
        >
            @csrf
            <input id="type" type="hidden" name="type" value="{{ __('torrent.torrent') }}" />
            <input id="id" type="hidden" name="id" value="{{ $torrent->id }}" />
            <input type="hidden" name="old_status" value="{{ $torrent->status }}" />
            <input
                type="hidden"
                name="status"
                value="{{ \App\Enums\ModerationStatus::REJECTED }}"
            />
            <p class="form__group">
                <textarea
                    id="message"
                    class="form__textarea"
                    name="message"
                    >{{ old('message') }}</textarea
                >
                <label for="message" class="form__label form__label__floating">
                    Rejection message
                </label>
            </p>
            <p class="form__group">
                <button class="form__button form__button--filled">
                    {{ __('common.moderation-reject') }}
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
