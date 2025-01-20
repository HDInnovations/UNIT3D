@props([
    'post',
])

<article class="post" id="post-{{ $post->id }}" x-data>
    <header class="post__header">
        <time
            class="post__datetime"
            datetime="{{ $post->created_at }}"
            title="{{ $post->created_at }}"
        >
            {{ $post->created_at?->diffForHumans() }}
        </time>
        @if (! Route::is('topics.show'))
            <span class="post__topic">
                {{ __('forum.in') }}
                <a href="{{ route('topics.show', ['id' => $post->topic->id]) }}">
                    {{ $post->topic->name }}
                </a>
            </span>
        @endif

        @if ($post->tips_sum_bon > 0)
            <dl class="post__tip-stats">
                <dt>{{ __('torrent.bon-tipped') }}</dt>
                <dd>{{ $post->tips_sum_bon ?? 0 }}</dd>
            </dl>
        @endif

        <a class="post__toolbar-overflow" tabindex="0">
            <i class="fa fas fa-ellipsis"></i>
        </a>
        <menu class="post__toolbar">
            <li class="post__toolbar-item">
                <form
                    class="post__tip"
                    role="form"
                    method="POST"
                    action="{{ route('users.post_tips.store', ['user' => auth()->user()]) }}"
                >
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}" />
                    <input
                        class="post__tip-input"
                        inputmode="numeric"
                        list="quick-tip-values"
                        name="bon"
                        pattern="[0-9]*"
                        placeholder="0"
                        type="text"
                        value="0"
                    />
                    <button
                        class="post__tip-button"
                        type="submit"
                        title="{{ __('forum.tip-this-post') }}"
                    >
                        Tip
                    </button>
                    <datalist id="quick-tip-values">
                        <option value="1000"></option>
                        <option value="2000"></option>
                        <option value="5000"></option>
                        <option value="10000"></option>
                        <option value="20000"></option>
                        <option value="50000"></option>
                        <option value="100000"></option>
                    </datalist>
                </form>
            </li>
            <li
                class="post__toolbar-item"
                x-data="likeButton({{ $post->id }}, {{ $post->likes_count }}, {{ $post->likes_exists }})"
            >
                <button class="votes__like" x-bind="button">
                    <i
                        x-bind="icon"
                        class="votes__like-icon {{ config('other.font-awesome') }} fa-thumbs-up"
                    ></i>
                    <span class="votes__like-count" x-text="likesCount"></span>
                </button>
            </li>
            <li
                class="post__toolbar-item"
                x-data="dislikeButton({{ $post->id }}, {{ $post->dislikes_count }}, {{ $post->dislikes_exists }})"
            >
                <button class="votes__dislike" x-bind="button">
                    <i
                        x-bind="icon"
                        class="votes__dislike-icon {{ config('other.font-awesome') }} fa-thumbs-down"
                    ></i>
                    <span class="votes__dislike-count" x-text="dislikesCount"></span>
                </button>
            </li>
            <li class="post__toolbar-item">
                <a
                    class="post__permalink"
                    href="{{ route('topics.permalink', ['topicId' => $post->topic_id, 'postId' => $post->id]) }}"
                    title="{{ __('forum.permalink') }}"
                >
                    <i class="{{ \config('other.font-awesome') }} fa-link"></i>
                </a>
            </li>
            @if (auth()->user()->group->is_modo || $post->topic->state === 'open')
                <li class="post__toolbar-item">
                    <button
                        class="post__quote"
                        title="{{ __('forum.quote') }}"
                        x-on:click="
                            document.getElementById('forum_reply_form').style.display = 'block';
                            input = document.getElementById('bbcode-content');
                            input.value += '[quote={{ \htmlspecialchars('@' . $post->user->username) }}]';
                            input.value += decodeURIComponent(escape(atob('{{ base64_encode($post->content) }}')));
                            input.value += '[/quote]';
                            input.dispatchEvent(new Event('input'));
                            input.focus();
                        "
                    >
                        <i class="{{ \config('other.font-awesome') }} fa-quote-left"></i>
                    </button>
                </li>
            @endif

            @if (auth()->user()->group->is_modo || ($post->user->id === auth()->id() && $post->topic->state === 'open'))
                <li class="post__toolbar-item">
                    <a
                        class="post__edit"
                        href="{{ route('posts.edit', ['id' => $post->id]) }}"
                        title="{{ __('common.edit') }}"
                    >
                        <i class="{{ \config('other.font-awesome') }} fa-pencil"></i>
                    </a>
                </li>
                <li class="post__toolbar-item">
                    <form
                        class="post__delete"
                        role="form"
                        method="POST"
                        action="{{ route('posts.destroy', ['id' => $post->id]) }}"
                        x-data="confirmation"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            class="post__delete-button"
                            type="submit"
                            title="{{ __('common.delete') }}"
                            x-on:click.prevent="confirmAction"
                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this post?') }}"
                        >
                            <i class="{{ \config('other.font-awesome') }} fa-trash"></i>
                        </button>
                    </form>
                </li>
            @endif
        </menu>
    </header>
    <aside class="post__aside">
        <figure class="post__figure">
            <img
                class="post__avatar"
                src="{{ $post->user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $post->user]) }}"
                alt=""
            />
        </figure>
        <x-user_tag class="post__author" :anon="false" :user="$post->user">
            <x-slot:appended-icons>
                @if ($post->user->isOnline())
                    <i
                        class="{{ config('other.font-awesome') }} fa-circle text-green"
                        title="Online"
                    ></i>
                @else
                    <i
                        class="{{ config('other.font-awesome') }} fa-circle text-red"
                        title="Offline"
                    ></i>
                @endif
                <a
                    href="{{ route('users.conversations.create', ['user' => auth()->user(), 'username' => $post->user->username]) }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-envelope text-info"></i>
                </a>
            </x-slot>
        </x-user_tag>
        @if (! empty($post->user->title))
            <p class="post__author-title">
                {{ $post->user->title }}
            </p>
        @endif

        <dl class="post__author-join">
            <dt>Joined</dt>
            <dd>
                <time
                    class="post__author-join-datetime"
                    datetime="{{ $post->user->created_at }}"
                    title="{{ $post->user->created_at }}"
                >
                    {{ date('d M Y', $post->user->created_at?->getTimestamp() ?? '') }}
                </time>
            </dd>
        </dl>
        <dl class="post__author-topics">
            <dt>
                <a href="{{ route('users.topics.index', ['user' => $post->user]) }}">
                    {{ __('forum.topics') }}
                </a>
            </dt>
            <dd>{{ $post->author_topics_count ?? '0' }}</dd>
        </dl>
        <dl class="post__author-posts">
            <dt>
                <a href="{{ route('users.posts.index', ['user' => $post->user]) }}">
                    {{ __('forum.posts') }}
                </a>
            </dt>
            <dd>{{ $post->author_posts_count ?? '0' }}</dd>
        </dl>
    </aside>
    <div
        class="post__content bbcode-rendered"
        x-ref="content"
        data-base64-bbcode="{{ base64_encode($post->content) }}"
    >
        @bbcode($post->content)
    </div>
    @if (! empty($post->user->signature))
        <footer class="post__footer" x-init>
            <p class="post__signature">
                @bbcode($post->user->signature)
            </p>
        </footer>
    @endif
</article>
