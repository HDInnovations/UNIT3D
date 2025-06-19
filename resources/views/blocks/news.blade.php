<section
    class="panelV2 blocks__news"
    x-data="{
        show: {{ Js::from($articles->contains(fn ($article) => $article->unread_news_exists)) }},
    }"
>
    <header class="panel__header" x-on:click="show = !show" style="cursor: pointer">
        <h2 class="panel__heading panel__heading--centered">
            @if ($articles->first()?->unread_news_exists)
                @joypixels(':rotating_light:')
                {{ __('blocks.new-news') }}
                {{ $articles->first()?->created_at?->diffForHumans() }}
                @joypixels(':rotating_light:')
            @else
                {{ __('blocks.check-news') }}
                {{ $articles->first()?->created_at?->diffForHumans() }}
            @endif
        </h2>
        <div class="panel__actions">
            <div class="panel__action">
                <a href="{{ route('articles.index') }}" class="form__button form__button--text">
                    {{ __('common.view-all') }}
                </a>
            </div>
        </div>
    </header>
    <div class="panel__body article-preview-wrapper" x-cloak x-show="show">
        @foreach ($articles as $article)
            <article class="article-preview">
                <header class="article-preview__header">
                    <h2 class="article-preview__title">
                        @if ($article->unread_news_exists)
                            <x-animation.notification />
                        @endif

                        <a
                            class="article-preview__link"
                            href="{{ route('articles.show', ['article' => $article]) }}"
                        >
                            {{ $article->title }}
                        </a>
                    </h2>
                    <time
                        class="article-preview__published-date"
                        datetime="{{ $article->created_at }}"
                        title="{{ $article->created_at }}"
                    >
                        {{ $article->created_at->diffForHumans() }}
                    </time>
                    <img
                        class="article-preview__image"
                        src="{{ $article->image ? route('authenticated_images.article_image', ['article' => $article]) : url('img/missing-image.png') }}"
                        alt=""
                    />
                </header>
                <p class="article-preview__content">
                    @joypixels(preg_replace('#\[[^\]]+\]#', '', Str::limit(e($article->content), 500, '...'), 150))
                </p>
                <a
                    href="{{ route('articles.show', ['article' => $article]) }}"
                    class="article-preview__read-more"
                >
                    {{ __('articles.read-more') }}
                </a>
            </article>
        @endforeach
    </div>
</section>
