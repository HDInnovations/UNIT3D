<li class="data-table__action">
    <button
        class="form__button form__button--filled"
        popovertarget="torrent-postpone-{{ $torrent->id }}"
    >
        <i class="{{ config('other.font-awesome') }} fa-pause"></i>
        {{ __('common.moderation-postpone') }}
    </button>
    <dialog id="torrent-postpone-{{ $torrent->id }}" class="dialog" popover>
        <h4 class="dialog__heading">
            {{ __('common.moderation-postpone') }} {{ __('torrent.torrent') }}:
            {{ $torrent->name }}
        </h4>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('staff.moderation.update', ['id' => $torrent->id]) }}"
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
                <textarea class="form__textarea" name="message" id="message">
{{ old('message') }}</textarea
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
                    class="form__button form__button--outlined"
                    type="button"
                    popovertarget="torrent-postpone-{{ $torrent->id }}"
                >
                    {{ __('common.cancel') }}
                </button>
            </p>
        </form>
    </dialog>
</li>
