@if ($poll && $poll->voters->where('user_id', '=', auth()->user()->id)->isEmpty())
    <div class="col-md-10 col-sm-10 col-md-offset-1">
        <div class="clearfix visible-sm-block"></div>
        <div class="panel panel-chat shoutbox">
            <div class="panel-heading">
                <h4><i class="{{ config('other.font-awesome') }} fa-chart-pie"></i> @lang('poll.poll')</h4>
            </div>
            <div class="panel-body">
                <h3 class="poll-title">{{ $poll->title }}</h3>
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
                            </div>
                        </div>
                    </form>
                    @if ($poll->multiple_choice)
                        <span class="badge-user text-bold text-red poll-note">@lang('poll.multiple-choice')</span>
                    @endif
            </div>
        </div>
    </div>
@endif
