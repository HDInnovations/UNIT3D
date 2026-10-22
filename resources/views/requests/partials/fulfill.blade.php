<li class="form__group form__group--short-horizontal">
    <button
        class="form__button form__button--outlined form__button--centered"
        popovertarget="request-fulfill"
    >
        {{ __('request.fulfill') }}
    </button>
    <dialog id="request-fulfill" class="dialog" popover>
        <h3 class="dialog__heading">
            {{ __('request.fill-request') }}
        </h3>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('requests.fills.store', ['torrentRequest' => $torrentRequest]) }}"
        >
            @csrf
            <p class="form__group">
                <input
                    id="torrent_id"
                    class="form__text"
                    name="torrent_id"
                    placeholder=" "
                    type="text"
                />
                <label for="torrent_id" class="form__label form__label--floating">
                    {{ __('request.enter-hash') }}
                </label>
            </p>
            <p class="form__group">
                <input type="hidden" name="filled_anon" value="0" />
                <input
                    type="checkbox"
                    class="form__checkbox"
                    id="filled_anon"
                    name="filled_anon"
                    value="1"
                />
                <label class="form__label" for="filled_anon">{{ __('common.anonymous') }}?</label>
            </p>
            <p class="form__group">
                <button class="form__button form__button--filled">
                    {{ __('request.fulfill') }}
                </button>
                <button
                    class="form__button form__button--outlined"
                    popovertarget="request-fulfill"
                    type="button"
                >
                    {{ __('common.cancel') }}
                </button>
            </p>
        </form>
    </dialog>
</li>
