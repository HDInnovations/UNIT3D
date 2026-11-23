<form
    class="form-horizontal"
    method="POST"
    action="{{ route('polls.votes.store', ['poll' => $poll]) }}"
>
    @csrf
    @if ($poll->multiple_choice)
        @foreach ($poll->options as $option)
            <p class="form__group">
                <input
                    id="option{{ $option->id }}"
                    class="form__checkbox"
                    type="checkbox"
                    name="options[]"
                    value="{{ $option->id }}"
                />
                <label class="form__label" for="option{{ $option->id }}">
                    {{ $option->name }}
                </label>
            </p>
        @endforeach
    @else
        @foreach ($poll->options as $option)
            <p class="form__group">
                <input
                    id="option{{ $option->id }}"
                    class="form__radio"
                    type="radio"
                    name="options[]"
                    value="{{ $option->id }}"
                    required
                />
                <label class="form__label" for="option{{ $option->id }}">
                    {{ $option->name }}
                </label>
            </p>
        @endforeach
    @endif
    <p class="form__group">
        <button class="form__button form__button--filled">
            {{ __('poll.vote') }}
        </button>
        <a
            class="form__button form__button--outlined"
            href="{{ route('polls.votes.index', ['poll' => $poll]) }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-chart-bar"></i>
            {{ __('poll.results') }}
        </a>
    </p>
</form>
