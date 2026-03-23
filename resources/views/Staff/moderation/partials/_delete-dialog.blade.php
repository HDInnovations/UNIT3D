<li class="data-table__action" x-data="dialog">
    <button class="form__button form__button--filled" x-bind="showDialog">
        <i class="{{ config('other.font-awesome') }} fa-thumbs-down"></i>
        {{ __('common.delete') }}
    </button>
    <dialog class="dialog" x-bind="dialogElement">
        <h4 class="dialog__heading">
            {{ __('common.delete') }} {{ __('torrent.torrent') }}: {{ $torrent->name }}
        </h4>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('torrents.destroy', ['id' => $torrent->id]) }}"
            x-bind="dialogForm"
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
                <button class="form__button form__button--filled">{{ __('common.delete') }}</button>
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
