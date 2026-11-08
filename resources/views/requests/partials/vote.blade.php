<li class="form__group form__group--short-horizontal">
    <button
        class="form__button form__button--filled form__button--centered"
        popovertarget="request-vote"
    >
        {{ __('request.vote') }}
    </button>
    <dialog id="request-vote" class="dialog" popover>
        <h3 class="dialog__heading">
            {{ __('request.vote-that') }}
        </h3>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('requests.bounties.store', ['torrentRequest' => $torrentRequest]) }}"
        >
            @csrf
            <input id="type" type="hidden" name="request_id" value="{{ $torrentRequest->id }}" />
            <p class="form__group">
                <input
                    id="seedbonus"
                    class="form__text"
                    inputmode="numeric"
                    name="seedbonus"
                    placeholder=" "
                    type="text"
                />
                <label for="seedbonus" class="form__label form__label--floating">
                    {{ __('request.enter-bp', ['min' => config('other.bon.min-bounty')]) }}
                </label>
            </p>
            <p class="form__group">
                <input type="hidden" name="anon" value="0" />
                <input type="checkbox" class="form__checkbox" id="anon" name="anon" value="1" />
                <label class="form__label" for="anon">{{ __('common.anonymous') }}?</label>
            </p>
            <p class="form__group">
                <button
                    class="form__button form__button--filled"
                    @if ($user->seedbonus < config('other.bon.min-bounty'))
                        disabled
                        title="{{ __('request.dont-have-bps') }}"
                    @endif
                >
                    {{ __('request.vote') }}
                </button>
                <button
                    class="form__button form__button--outlined"
                    type="button"
                    popovertarget="request-vote"
                >
                    {{ __('common.cancel') }}
                </button>
            </p>
        </form>
    </dialog>
</li>
