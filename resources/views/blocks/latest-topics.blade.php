<section class="panelV2 blocks__latest-topics">
    <header class="panel__header">
        <h2 class="panel__heading">
            <a href="{{ route('topics.index') }}">
                {{ __('blocks.latest-topics') }}
            </a>
        </h2>
    </header>
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
</section>
