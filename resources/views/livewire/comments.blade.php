<section class="panelV2" id="comments">
    <h4 class="panel__heading">
        <i class="{{ config('other.font-awesome') }} fa-comment"></i>
        {{ __('common.comments') }}
    </h4>
    <div class="panel__body">
        <form wire:submit="postComment" class="form new-comment" x-data="toggle">
            <p class="form__group">
                <textarea
                    name="comment"
                    id="new-comment__textarea"
                    class="form__textarea"
                    aria-describedby="new-comment__textarea-hint"
                    wire:model="newCommentState"
                    required
                    x-on:focus="toggleOn"
                ></textarea>
                <label for="new-comment__textarea" class="form__label form__label--floating">
                    @error('newCommentState')
                        <strong>{{ __('common.error') }}:</strong>
                    @enderror

                    Add a comment...
                </label>
                @error('newCommentState')
                    <span class="form__hint" id="new-comment__textarea-hint">{{ $message }}</span>
                @enderror
            </p>
            <p class="form__group" x-show="isToggledOn" x-cloak>
                <input type="checkbox" id="anon" class="form__checkbox" wire:model.live="anon" />
                <label for="anon" class="form__label">{{ __('common.anonymous') }}?</label>
            </p>
            <p class="form__group" x-show="isToggledOn" x-cloak>
                <button type="submit" class="form__button form__button--filled">Comment</button>
                <button
                    type="reset"
                    class="form__button form__button--text"
                    x-on:click="toggleOff"
                >
                    {{ __('common.cancel') }}
                </button>
            </p>
        </form>
        <ol class="comment-list">
            @forelse ($comments as $comment)
                <livewire:comment :model="$model" :comment="$comment" :key="$comment->id" />
            @empty
                <li>
                    <i class="{{ config('other.font-awesome') }} fa-frown"></i>
                    {{ __('common.no-comments') }}!
                </li>
            @endforelse
        </ol>
        @if ($comments->hasMorePages())
            <div class="text-center">
                <button class="form__button form__button--filled" wire:click.prevent="loadMore">
                    Load more comments
                </button>
            </div>
        @endif
    </div>
</section>
