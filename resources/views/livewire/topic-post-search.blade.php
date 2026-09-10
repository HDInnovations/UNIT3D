<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ $topic->name }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="search"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="search"
                        placeholder=" "
                    />
                    <label for="search" class="form__label form__label--floating">
                        {{ __('common.search') }}
                    </label>
                </div>
            </div>
        </div>
    </header>
    @if ($topic->approved || $topic->denied || $topic->solved || $topic->invalid || $topic->bug || $topic->suggestion || $topic->implemented)
        <ul class="topic-tags">
            <li class="topic-tag">
                <i class="{{ config('other.font-awesome') }} fa-tags"></i>
            </li>
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
    @endif

    {{ $posts->links('partials.pagination') }}
    <div class="panel__body">
        @if ($posts->count() > 0)
            <ol class="topic-posts">
                @foreach ($pinnedPosts as $post)
                    <li class="topic-posts__item">
                        <x-forum.post :post="$post" />
                    </li>
                @endforeach

                @if ($pinnedPosts->isNotEmpty())
                    <hr class="topic-posts__pinned-separator" />
                @endif

                @foreach ($posts as $post)
                    <li class="topic-posts__item">
                        <x-forum.post :post="$post" />
                    </li>
                @endforeach

                @if ($pinnedPosts->isNotEmpty())
                    <hr class="topic-posts__pinned-separator" />
                @endif

                @foreach ($pinnedPosts as $post)
                    <li class="topic-posts__item">
                        <x-forum.post :post="$post" />
                    </li>
                @endforeach
            </ol>
        @else
            No topics.
        @endif
    </div>
    {{ $posts->links('partials.pagination') }}
</section>
