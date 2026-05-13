@php
    $canEditSimilarNote = auth()->user()->group->is_modo || auth()->user()->group->is_torrent_modo;
@endphp

@if (filled($meta->note) || $canEditSimilarNote)
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-sticky-note"></i>
                Similar page note
            </h2>
            @if ($canEditSimilarNote)
                <div class="panel__actions">
                    <div class="panel__action">
                        <button
                            class="form__button form__button--text"
                            popovertarget="similar-note-edit"
                        >
                            {{ filled($meta->note) ? __('common.edit') : __('common.add') }}
                        </button>
                    </div>
                </div>
            @endif
        </header>
        @if (filled($meta->note))
            <div class="panel__body bbcode-rendered">
                @bbcode($meta->note)
            </div>
        @elseif ($canEditSimilarNote)
            <div class="panel__body">No note has been set.</div>
        @endif

        @if ($canEditSimilarNote)
            <dialog id="similar-note-edit" class="dialog" popover>
                <h4 class="dialog__heading">Edit similar page note</h4>
                <form
                    class="form"
                    action="{{ route('torrents.similar.update', ['category' => $category, 'metaId' => $meta->id]) }}"
                    method="post"
                >
                    @csrf
                    @method('PATCH')
                    <p class="form__group">
                        {{-- format-ignore-start --}}
                        <textarea
                            class="form__textarea"
                            id="similar-page-note"
                            maxlength="65535"
                            name="note"
                            rows="6"
                        >{{ old('note', $meta->note) }}</textarea>
                        {{-- format-ignore-end --}}
                        <label class="form__label form__label--floating" for="similar-page-note">
                            Similar page note
                        </label>
                    </p>
                    <p class="form__group">
                        <button class="form__button form__button--filled" type="submit">
                            {{ __('common.save') }}
                        </button>
                        <button
                            class="form__button form__button--outlined"
                            popovertarget="similar-note-edit"
                            type="button"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </p>
                </form>
            </dialog>
        @endif
    </section>
@endif
