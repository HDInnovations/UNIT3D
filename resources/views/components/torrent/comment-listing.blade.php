@props([
    'comment',
])

<article class="comment" id="comment-{{ $comment->id }}" x-data>
    <header class="comment__header">
        <time
            class="comment__datetime"
            datetime="{{ $comment->created_at }}"
            title="{{ $comment->created_at }}"
        >
            {{ $comment->created_at?->diffForHumans() }}
        </time>

        <span class="comment__topic">
            On

            @switch($comment->commentable_type)
                @case(\App\Models\Torrent::class)
                    {{ __('torrent.torrent') }}
                    <a href="{{ route('torrents.show', ['id' => $comment->commentable->id]) }}">
                        {{ $comment->commentable->name }}
                    </a>

                    @break
                @case(\App\Models\TorrentRequest::class)
                    {{ __('request.request') }}
                    <a
                        href="{{ route('requests.show', ['torrentRequest' => $comment->commentable]) }}"
                    >
                        {{ $comment->commentable->name }}
                    </a>

                    @break
                @default
                    {{ __('common.unknown') }}
            @endswitch
        </span>
    </header>
    <aside class="comment__aside">
        <x-user-tag class="comment__author" :anon="$comment->anon" :user="$comment->user">
            <x-slot:appended-icons>
                @if ($comment->user->isOnline())
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
                    href="{{ route('users.conversations.create', ['user' => auth()->user(), 'username' => $comment->user->username]) }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-envelope text-info"></i>
                </a>
            </x-slot>
        </x-user-tag>
        @if (! empty($comment->user->title) && ! $comment->anon)
            <p class="comment__author-title">
                {{ $comment->user->title }}
            </p>
        @endif
    </aside>
    <div class="comment__content bbcode-rendered">
        @bbcode($comment->content)
    </div>
</article>
