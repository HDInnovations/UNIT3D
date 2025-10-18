<li class="comment__list-item">
    <article id="comment-{{ $comment->id }}" class="comment">
        <header class="comment__header">
            <time
                class="comment__datetime"
                datetime="{{ $comment->created_at }}"
                title="{{ $comment->created_at }}"
            >
                {{ $comment->created_at?->diffForHumans() }}
            </time>
            <menu class="comment__toolbar">
                <li class="comment__toolbar-item">
                    <button
                        @if ($comment->isParent())
                            wire:click="$toggle('isReplying')"
                        @else
                            wire:click="$parent.$toggle('isReplying')"
                        @endif
                        class="comment__reply"
                    >
                        <abbr class="comment__reply-abbr" title="Reply to this comment">
                            <i class="{{ config('other.font-awesome') }} fa-reply"></i>
                            <span class="sr-only">__('pm.reply')</span>
                        </abbr>
                    </button>
                </li>

                <li class="comment__toolbar-item">
                    <button
                        class="post__quote"
                        title="{{ __('forum.quote') }}"
                        x-on:click="
                            input = document.getElementById(
                                '{{ $comment->isParent() ? 'new-comment__textarea' : 'reply-comment' }}'
                            );
                            if (input.value !== '') {
                                input.value += '\n\n';
                            }
                            input.value +=
                                '[quote={{ $comment->anon ? 'Anonymous' : '@' . $comment->user->username }}]\n';
                            input.value += decodeURIComponent(
                                escape(atob('{{ base64_encode($comment->content) }}'))
                            );
                            input.value += '\n[/quote]\n\n';
                            input.dispatchEvent(new Event('input'));
                            input.focus();
                        "
                    >
                        <i class="{{ \config('other.font-awesome') }} fa-quote-left"></i>
                    </button>
                </li>
                @if ($comment->user_id === auth()->id() || auth()->user()->group->is_modo)
                    <li class="comment__toolbar-item">
                        <button wire:click="$toggle('isEditing')" class="comment__edit">
                            <abbr
                                class="comment__edit-abbr"
                                title="{{ __('common.edit-your-comment') }}"
                            >
                                <i class="{{ config('other.font-awesome') }} fa-pencil"></i>
                                <span class="sr-only">__('common.edit')</span>
                            </abbr>
                        </button>
                    </li>
                    <li class="comment__toolbar-item">
                        <button
                            class="comment__delete-button"
                            x-on:click="confirmCommentDeletion"
                            x-data="{
                               confirmCommentDeletion () {
                                   if (window.confirm('You sure?')) {
                                        @this.call('deleteComment')
                                   }
                               }
                           }"
                        >
                            <abbr
                                class="comment__delete-abbr"
                                title="{{ __('common.delete-your-comment') }}"
                            >
                                <i class="{{ config('other.font-awesome') }} fa-trash"></i>
                                <span class="sr-only">__('common.delete')</span>
                            </abbr>
                        </button>
                    </li>
                @endif
            </menu>
        </header>
        <aside class="comment__aside">
            <figure class="comment__figure" style="text-align: center">
                <img
                    class="comment__avatar"
                    style="width: 50%"
                    src="{{ ! $comment->anon && $comment->user->image !== null ? route('authenticated_images.user_avatar', ['user' => $comment->user]) : url('/img/profile.png') }}"
                    alt=""
                />
            </figure>
            <x-user-tag
                class="comment__author"
                :anon="$comment->anon"
                :user="$comment->user"
            ></x-user-tag>
            @if (! $comment->anon && ! empty($comment->user->title))
                <p class="comment__author-title">
                    {{ $comment->user->title }}
                </p>
            @endif
        </aside>
        @if ($isEditing)
            <form wire:submit="editComment" class="form edit-comment">
                <p class="form__group">
                    <textarea
                        name="comment"
                        id="edit-comment"
                        class="form__textarea"
                        aria-describedby="edit-comment__textarea-hint"
                        wire:model="editState"
                        required
                    ></textarea>
                    <label for="edit-comment" class="form__label form__label--floating">
                        @error('editState')
                            <strong>{{ __('common.error') }}:</strong>
                        @enderror

                        Edit your comment...
                    </label>
                    @error('editState')
                        <span class="form__hint" id="edit-comment__textarea-hint">
                            {{ $message }}
                        </span>
                    @enderror
                </p>
                <p class="form__group">
                    <button type="submit" class="form__button form__button--filled">
                        {{ __('common.edit') }}
                    </button>
                    <button
                        type="button"
                        wire:click="$toggle('isEditing')"
                        class="form__button form__button--text"
                    >
                        {{ __('common.cancel') }}
                    </button>
                </p>
            </form>
        @else
            <div class="comment__content bbcode-rendered">
                @bbcode($comment->content)
            </div>
        @endif
    </article>

    @if ($comment->isParent())
        <section class="comment__replies">
            <h5 class="sr-only">Replies</h5>
            @if ($comment->children()->exists())
                <ul class="comment__reply-list">
                    @foreach ($comment->children as $child)
                        <livewire:comment :comment="$child" :key="$child->id" />
                    @endforeach
                </ul>
            @endif

            @if ($isReplying)
                <form wire:submit="postReply" class="form reply-comment" x-data="toggle">
                    <p class="form__group">
                        <textarea
                            name="comment"
                            id="reply-comment"
                            class="form__textarea"
                            aria-describedby="reply-comment__textarea-hint"
                            wire:model="replyState"
                            required
                            x-on:focus="toggleOn"
                        ></textarea>
                        <label for="reply-comment" class="form__label form__label--floating">
                            @error('editState')
                                <strong>{{ __('common.error') }}:</strong>
                            @enderror

                            Reply to parent comment...
                        </label>
                        @error('replyState')
                            <span class="form__hint" id="reply-comment__textarea-hint">
                                {{ $message }}
                            </span>
                        @enderror
                    </p>
                    <p class="form__group" x-show="isToggledOn" x-cloak>
                        <input
                            type="checkbox"
                            id="reply-anon"
                            class="form__checkbox"
                            wire:model.live="anon"
                        />
                        <label for="reply-anon" class="form__label">
                            {{ __('common.anonymous') }}?
                        </label>
                    </p>
                    <p class="form__group" x-show="isToggledOn" x-cloak>
                        <button type="submit" class="form__button form__button--filled">
                            {{ __('common.comment') }}
                        </button>
                        <button
                            type="reset"
                            wire:click="$toggle('isReplying')"
                            class="form__button form__button--text"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </p>
                </form>
            @endif
        </section>
    @endif
</li>
