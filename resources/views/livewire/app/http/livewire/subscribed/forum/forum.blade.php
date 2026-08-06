<section class="panelV2">
    <h2 class="panel__heading">{{ __('forum.forums') }}</h2>
    {{ $forums->links('partials.pagination') }}
    @if ($forums->count() > 0)
        <ul class="subforum-listings">
            @foreach ($forums as $forum)
                <li class="subforum-listings__item">
                    <x-forum.subforum-listing :subforum="$forum" />
                </li>
            @endforeach
        </ul>
    @else
        <div class="panel__body">No forums in category.</div>
    @endif
    {{ $forums->links('partials.pagination') }}
</section>
