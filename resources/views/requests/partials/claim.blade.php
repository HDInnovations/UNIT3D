<li class="form__group form__group--short-horizontal" x-data="dialog">
    <button class="form__button form__button--outlined form__button--centered" x-bind="showDialog">
        {{ __('request.claim') }}
    </button>
    <dialog class="dialog" x-bind="dialogElement">
        <h3 class="dialog__heading">{{ __('request.claim') }}</h3>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('requests.claims.store', ['torrentRequest' => $torrentRequest]) }}"
            x-bind="dialogForm"
        >
            @csrf
            <p class="form__group">
                <input type="hidden" name="anon" value="0" />
                <input
                    id="anon_claim"
                    type="checkbox"
                    class="form__checkbox"
                    name="anon"
                    value="1"
                />
                <label class="form__label" for="anon_claim">{{ __('common.anonymous') }}?</label>
            </p>
            <p class="form__group">
                <button class="form__button form__button--filled">
                    {{ __('request.claim-now') }}
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
