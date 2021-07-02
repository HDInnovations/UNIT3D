<form class="form-horizontal" method="POST" action="/polls/vote">
    @csrf
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! csrf_field() !!}

    @if ($poll->multiple_choice)
        @foreach ($poll->options as $option)
            <div class="poll-item">
                <label>
                    <input type="checkbox" name="option[]" value="{{ $option->id }}">
                    <span class="badge-user">{{ $option->name }}</span>
                </label>
            </div>
        @endforeach
    @else
        @foreach ($poll->options as $option)
            <div class="poll-item">
                <label>
                    <input type="radio" name="option[]" value="{{ $option->id }}" required>
                    <span class="badge-user">{{ $option->name }}</span>
                </label>
            </div>
        @endforeach
    @endif

    <div class="poll form-group">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">@lang('poll.vote')</button>
            <a class="btn btn-success" href="{{ route('poll_results', ['id' => $poll->id]) }}" role="button"><i
                        class="{{ config('other.font-awesome') }} fa-bar-chart"
                        aria-hidden="true"> @lang('poll.results')</i></a>
        </div>
    </div>
</form>
