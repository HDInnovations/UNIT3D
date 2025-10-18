<div class="form__group form__group--short-horizontal" x-data="dialog">
    <button
        class="form__button form__button--outlined form__button--centered"
        x-bind="showDialog"
    >
        {{ __('common.report') }}
    </button>
    <dialog class="dialog" x-bind="dialogElement">
        <h3 class="dialog__heading">{{ __('request.report') }}: {{ $torrentRequest->name }}</h3>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('report_request', ['id' => $torrentRequest->id]) }}"
            x-bind="dialogForm"
        >
            @csrf
            <input id="type" type="hidden" name="title" value="{{ $torrentRequest->name }}" />
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
                    formmethod="dialog"
                    formnovalidate
                    class="form__button form__button--outlined"
                >
                    {{ __('common.cancel') }}
                </button>
            </p>
        </form>
    </dialog>
</div>
