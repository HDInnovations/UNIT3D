<section class="panelV2 blocks__posts">
    <header class="panel__header">
        <h2 class="panel__heading">
            <a href="{{ route('posts.index') }}">
                {{ __('blocks.latest-posts') }}
            </a>
        </h2>
    </header>
    <div class="panel__body">
        @if ($posts->count() > 0)
            <ul class="topic-posts">
                @foreach ($posts as $post)
                    <li class="post-listings__item">
                        <x-forum.post :post="$post" />
                    </li>
                @endforeach
            </ul>
        @else
            No posts.
        @endif
    </div>
</section>
