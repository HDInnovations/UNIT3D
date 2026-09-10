<li class="data-table__action">
    <button
        class="form__button form__button--filled"
        popovertarget="torrent-delete-{{ $torrent->id }}"
    >
        <i class="{{ config('other.font-awesome') }} fa-thumbs-down"></i>
        {{ __('common.delete') }}
    </button>
    <dialog id="torrent-delete-{{ $torrent->id }}" class="dialog" popover>
        <h4 class="dialog__heading">
            {{ __('common.delete') }} {{ __('torrent.torrent') }}: {{ $torrent->name }}
        </h4>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('torrents.destroy', ['id' => $torrent->id]) }}"
        >
            @csrf
            @method('DELETE')
            <p class="form__group">
                <input id="type" type="hidden" name="type" value="{{ __('torrent.torrent') }}" />
                <input id="id" type="hidden" name="id" value="{{ $torrent->id }}" />
            </p>
            <p class="form__group">
                <textarea class="form__textarea" name="message" id="message"></textarea>
                <label class="form__label form__label--floating" for="message">
                    Deletion reason
                </label>
            </p>
            <p class="form__group">
                <button class="form__button form__button--filled">
                    {{ __('common.delete') }}
                </button>
                <button
                    class="form__button form__button--outlined"
                    type="button"
                    popovertarget="torrent-delete-{{ $torrent->id }}"
                >
                    {{ __('common.cancel') }}
                </button>
            </p>
        </form>
    </dialog>
</li>
