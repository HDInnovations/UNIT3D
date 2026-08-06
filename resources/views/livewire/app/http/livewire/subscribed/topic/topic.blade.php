<section class="panelV2">
    <h2 class="panel__heading">{{ __('forum.topics') }}</h2>
    {{ $topics->links('partials.pagination') }}
    @if ($topics->count() > 0)
        <ul class="topic-listings">
            @foreach ($topics as $topic)
                <li class="topic-listings__item">
                    <x-forum.topic-listing :topic="$topic" />
                </li>
            @endforeach
        </ul>
    @else
        <div class="panel__body">No topics.</div>
    @endif
    {{ $topics->links('partials.pagination') }}
</section>
