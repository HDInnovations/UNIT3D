@props([
    'topic',
])

<article
    @class([
        'topic-listing',
        'topic-listing--read' => $topic->reads->first()?->last_read_post_id === $topic->last_post_id,
        'topic-listing--unread' => $topic->reads->first()?->last_read_post_id !== $topic->last_post_id,
    ])
    class="topic-listing"
>
    <header class="topic-listing__header">
        <h2 class="topic-listing__heading">
            @if ($topic->reads->isEmpty())
                <a
                    class="topic-listing__link"
                    href="{{ route('topics.show', ['id' => $topic->id]) }}"
                >
                    {{ $topic->name }}
                </a>
            @else
                <a
                    class="topic-listing__link"
                    href="{{ route('topics.permalink', ['topicId' => $topic->id, 'postId' => $topic->reads->first()?->last_read_post_id ?? 0]) }}"
                >
                    {{ $topic->name }}
                </a>
            @endif
        </h2>
        <ul class="topic-tags">
            @if ($topic->approved)
                <li class="topic-tag topic-tag--approved">{{ __('forum.approved') }}</li>
            @endif

            @if ($topic->denied)
                <li class="topic-tag topic-tag--denied">{{ __('forum.denied') }}</li>
            @endif

            @if ($topic->solved)
                <li class="topic-tag topic-tag--solved">{{ __('forum.solved') }}</li>
            @endif

            @if ($topic->invalid)
                <li class="topic-tag topic-tag--invalid">{{ __('forum.invalid') }}</li>
            @endif

            @if ($topic->bug)
                <li class="topic-tag topic-tag--bug">{{ __('forum.bug') }}</li>
            @endif

            @if ($topic->suggestion)
                <li class="topic-tag topic-tag--suggestion">{{ __('forum.suggestion') }}</li>
            @endif

            @if ($topic->implemented)
                <li class="topic-tag topic-tag--implemented">{{ __('forum.implemented') }}</li>
            @endif
        </ul>
        <article class="topic-listing__created-post">
            <address class="topic-listing__created-author">
                @if ($topic->user === null)
                    {{ __('common.unknown') }}
                @else
                    <a href="{{ route('users.show', ['user' => $topic->user]) }}">
                        {{ $topic->user->username }}
                    </a>
                @endif
            </address>
            <time
                class="topic-listing__created-datetime"
                datetime="{{ $topic->created_at }}"
                title="{{ $topic->created_at }}"
            >
                <a
                    class="topic-listing__created-link"
                    href="{{ route('topics.show', ['id' => $topic->id]) }}"
                >
                    {{ $topic->created_at?->diffForHumans() ?? __('common.unknown') }}
                </a>
            </time>
        </article>
    </header>
    <figure class="topic-listing__figure">
        @if ($topic->priority || $topic->state === 'close')
            <span class="topic-listing__icon">
                @if ($topic->priority)
                    <abbr title="{{ __('common.sticked') }}">
                        <i class="{{ config('other.font-awesome') }} fa-thumbtack"></i>
                    </abbr>
                @endif

                @if ($topic->state === 'close')
                    <abbr title="{{ __('user.locked') }}">
                        <i class="{{ config('other.font-awesome') }} fa-lock"></i>
                    </abbr>
                @endif
            </span>
        @else
            <i class="fad fa-comments topic-listing__icon"></i>
        @endif
        <figcaption class="topic-listing__forum">
            {{ $topic->forum->name }}
        </figcaption>
    </figure>
    <dl class="topic-listing__post-stats">
        <dt>{{ __('forum.replies') }}</dt>
        <dd>{{ $topic->num_post - 1 }}</dd>
    </dl>
    <dl class="topic-listing__view-stats">
        <dt>{{ __('forum.views') }}</dt>
        <dd>{{ $topic->views }}</dd>
    </dl>
    <article class="topic-listing__latest-post">
        <address class="topic-listing__latest-author">
            @if ($topic->latestPoster === null)
                {{ __('common.unknown') }}
            @else
                <a
                    class="topic-listing__latest-author-link"
                    href="{{ route('users.show', ['user' => $topic->latestPoster]) }}"
                >
                    {{ $topic->latestPoster->username }}
                </a>
            @endif
        </address>
        <time
            class="topic-listing__latest-datetime"
            datetime="{{ $topic->last_post_created_at }}"
            title="{{ $topic->last_post_created_at }}"
        >
            <a
                class="topic-listing__latest-post-link"
                href="{{ route('topics.latestPermalink', ['id' => $topic->id]) }}"
            >
                {{ $topic->last_post_created_at?->diffForHumans() ?? __('common.unknown') }}
            </a>
        </time>
    </article>
</article>
