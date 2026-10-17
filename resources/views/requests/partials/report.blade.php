<div class="form__group form__group--short-horizontal">
    <button
        class="form__button form__button--outlined form__button--centered"
        popovertarget="request-report"
    >
        {{ __('common.report') }}
    </button>
    <dialog id="request-report" class="dialog" popover>
        <h3 class="dialog__heading">{{ __('request.report') }}: {{ $torrentRequest->name }}</h3>
        <form class="dialog__form" method="POST" action="{{ route('reports.store') }}">
            @csrf
            <input type="hidden" name="reported_request_id" value="{{ $torrentRequest->id }}" />
            <p class="form__group">
                <textarea
                    id="message"
                    class="form__text"
                    name="message"
                    placeholder=" "
                    type="text"
                ></textarea>
                <label for="message" class="form__label form__label--floating">
                    {{ __('request.reason') }}
                </label>
            </p>
            <p class="form__group">
                <button class="form__button form__button--filled">
                    {{ __('request.report') }}
                </button>
                <button
                    class="form__button form__button--outlined"
                    type="button"
                    popovertarget="request-report"
                >
                    {{ __('common.cancel') }}
                </button>
            </p>
        </form>
    </dialog>
</div>
