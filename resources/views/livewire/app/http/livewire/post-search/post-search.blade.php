<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('common.latest-posts') }}</h2>
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
    {{ $posts->links('partials.pagination') }}
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
    {{ $posts->links('partials.pagination') }}
</section>
