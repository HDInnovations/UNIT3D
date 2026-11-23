@props([
    'subforum',
])

<article class="subforum-listing">
    <header class="subforum-listing__header">
        <h3 class="subforum-listing__heading">
            <a
                class="subforum-listing__link"
                href="{{ route('forums.show', ['id' => $subforum->id]) }}"
            >
                {{ $subforum->name }}
            </a>
        </h3>
        <p class="subforum-listing__description">
            {{ $subforum->description }}
        </p>
    </header>
    <figure class="subforum-listing__figure">
        <i class="fad fa-comments subforum-listing__icon"></i>
    </figure>
    <dl class="subforum-listing__topic-stats">
        <dt>{{ __('forum.topics') }}</dt>
        <dd>{{ $subforum->num_topic ?: 0 }}</dd>
    </dl>
    <dl class="subforum-listing__post-stats">
        <dt>{{ __('forum.posts') }}</dt>
        <dd>{{ $subforum->num_post ?: 0 }}</dd>
    </dl>
    <article class="subforum-listing__latest-topic">
        @if ($subforum->lastRepliedTopic !== null)
            <p class="subforum-listing__latest-heading">
                <a
                    class="subforum-listing__latest-link"
                    href="{{ route('topics.show', ['id' => $subforum->lastRepliedTopic->id]) }}"
                >
                    {{ $subforum->lastRepliedTopic->name }}
                </a>
            </p>
        @endif

        <time
            class="subforum-listing__latest-datetime"
            datetime="{{ $subforum->updated_at }}"
            title="{{ $subforum->updated_at }}"
        >
            @if ($subforum->lastRepliedTopic === null)
                {{ $subforum->updated_at?->diffForHumans() ?? __('common.unknown') }}
            @else
                <a
                    class="subforum-listing__latest-post-link"
                    href="{{ route('topics.latestPermalink', ['id' => $subforum->lastRepliedTopic->id]) }}"
                >
                    {{ $subforum->updated_at?->diffForHumans() ?? __('common.unknown') }}
                </a>
            @endif
        </time>
        @if ($subforum->lastRepliedTopic !== null && $subforum->latestPoster !== null)
            <address class="subforum-listing__latest-author">
                <a
                    class="subforum-listing__latest-author-link"
                    href="{{ route('users.show', ['user' => $subforum->latestPoster]) }}"
                >
                    {{ $subforum->latestPoster->username }}
                </a>
            </address>
        @endif
    </article>
</article>
