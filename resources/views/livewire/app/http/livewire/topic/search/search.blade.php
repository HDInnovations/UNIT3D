<article class="sidebar2">
    <div>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('common.latest-topics') }}</h2>
            {{ $topics->links('partials.pagination') }}
            @if ($topics->count() > 0)
                <ul class="topic-listings">
                    @foreach ($topics as $topic)
                        <li class="topic-listings__item">
                            <x-forum.topic-listing :topic="$topic" />
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="panel__body">No topics.</div>
            @endif
            {{ $topics->links('partials.pagination') }}
        </section>
    </div>
    <aside>
        <section class="panelV2">
            <h2 class="panel__heading">
                {{ __('common.actions') }}
            </h2>
            <div class="panel__body">
                <form class="form" action="{{ route('topic_reads.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="catchup_type" value="all" />
                    <p class="form__group form__group--horizontal">
                        <button
                            class="form__button form__button--filled form__button--centered"
                            title="Mark all topics as read"
                        >
                            Mark all read
                        </button>
                    </p>
                </form>
            </div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.filters') }}</h2>
            <div class="panel__body">
                <form class="form" x-data x-on:submit.prevent>
                    <p class="form__group">
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
                    </p>
                    <p class="form__group">
                        <select
                            name="category"
                            id="category"
                            class="form__select"
                            wire:model.live="forumId"
                        >
                            <option value="">Any</option>

                            @foreach ($forumCategories as $category)
                                <optgroup label="{{ $category->name }}">
                                    @foreach ($category->forums as $forum)
                                        <option value="{{ $forum->id }}">
                                            {{ $forum->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="category">
                            {{ __('common.forum') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select id="read" class="form__select" name="read" wire:model.live="read">
                            <option value="" selected default>Any</option>
                            <option value="some">With unread posts</option>
                            <option value="none">Newly added</option>
                            <option value="all">Fully read</option>
                        </select>
                        <label class="form__label form__label--floating" for="read">Activity</label>
                    </p>
                    <p class="form__group">
                        <select
                            id="sorting"
                            class="form__select"
                            name="sorting"
                            wire:model.live="label"
                        >
                            <option value="" selected default>Any</option>
                            <option value="approved">
                                {{ __('forum.approved') }}
                            </option>
                            <option value="implemented">
                                {{ __('forum.implemented') }}
                            </option>
                            <option value="solved">
                                {{ __('forum.solved') }}
                            </option>
                            <option value="denied">
                                {{ __('forum.denied') }}
                            </option>
                            <option value="invalid">
                                {{ __('forum.invalid') }}
                            </option>
                            <option value="bug">
                                {{ __('forum.bug') }}
                            </option>
                            <option value="suggestion">
                                {{ __('forum.suggestion') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="sorting">
                            {{ __('forum.label') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="sorting"
                            class="form__select"
                            name="sorting"
                            required
                            wire:model.live="sortField"
                        >
                            <option value="last_post_created_at">
                                {{ __('forum.updated-at') }}
                            </option>
                            <option value="created_at">
                                {{ __('forum.created-at') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="sorting">
                            {{ __('common.sort') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="direction"
                            class="form__select"
                            name="direction"
                            required
                            wire:model.live="sortDirection"
                        >
                            <option value="desc">
                                {{ __('common.descending') }}
                            </option>
                            <option value="asc">
                                {{ __('common.ascending') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="direction">
                            {{ __('common.direction') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="direction"
                            class="form__select"
                            name="direction"
                            wire:model.live="state"
                        >
                            <option value="" selected default>Any</option>
                            <option value="open">
                                {{ __('forum.open') }}
                            </option>
                            <option value="close">
                                {{ __('forum.closed') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="direction">
                            {{ __('forum.state') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="direction"
                            class="form__select"
                            name="direction"
                            wire:model.live="subscribed"
                        >
                            <option value="" selected default>Any</option>
                            <option value="include">
                                {{ __('forum.subscribed') }}
                            </option>
                            <option value="exclude">
                                {{ __('forum.not-subscribed') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="direction">
                            {{ __('common.subscriptions') }}
                        </label>
                    </p>
                </form>
            </div>
        </section>
    </aside>
</article>
