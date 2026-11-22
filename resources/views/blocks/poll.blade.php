@if ($poll &&$poll->votes()->where('user_id', '=', auth()->id())->doesntExist())
    <section class="panelV2 blocks__poll">
        <h2 class="panel__heading">{{ __('poll.poll') }}: {{ $poll->title }}</h2>
        <div class="panel__body">
            @include('poll.forms.vote')
        </div>
    </section>
@endif
