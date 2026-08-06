<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.user-notes') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <button class="form__button form__button--text" popovertarget="note-add">
                    {{ __('common.add') }}
                </button>
                <dialog id="note-add" class="dialog" popover>
                    <h3 class="dialog__heading">Note user: {{ $user->username }}</h3>
                    <form class="dialog__form">
                        <p class="form__group">
                            <textarea
                                id="message"
                                class="form__textarea"
                                name="message"
                                placeholder=" "
                                wire:model="message"
                            ></textarea>
                            <label class="form__label form__label--floating" for="message">
                                Note
                            </label>
                        </p>
                        <p class="form__group">
                            <button
                                class="form__button form__button--filled"
                                wire:click="store"
                                type="button"
                                popovertarget="note-add"
                            >
                                {{ __('common.save') }}
                            </button>
                            <button
                                class="form__button form__button--outlined"
                                type="button"
                                popovertarget="note-add"
                            >
                                {{ __('common.cancel') }}
                            </button>
                        </p>
                    </form>
                </dialog>
            </div>
        </div>
    </header>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('common.staff') }}</th>
                    <th>{{ __('user.note') }}</th>
                    <th>{{ __('user.created-on') }}</th>
                    <th>{{ __('torrent.updated_at') }}</th>
                    <th>{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notes as $note)
                    <tr x-data="userNote" data-note-id="{{ $note->id }}">
                        <td>
                            <x-user-tag :anon="false" :user="$note->staff" />
                        </td>
                        {{-- format-ignore-start --}}<td style="white-space: pre-wrap">@linkify($note->message)</td>{{-- format-ignore-end --}}
                        <td>
                            <time
                                datetime="{{ $note->created_at }}"
                                title="{{ $note->created_at }}"
                            >
                                {{ $note->created_at->diffForHumans() }}
                            </time>
                        </td>
                        <td>
                            <time
                                datetime="{{ $note->updated_at }}"
                                title="{{ $note->updated_at }}"
                            >
                                {{ $note->updated_at->diffForHumans() }}
                            </time>
                        </td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action" data-note-id="{{ $note->id }}">
                                    <button
                                        class="form__button form__button--text"
                                        popovertarget="note-edit-{{ $note->id }}"
                                    >
                                        {{ __('common.edit') }}
                                    </button>
                                    <dialog id="note-edit-{{ $note->id }}" class="dialog" popover>
                                        <h3 class="dialog__heading">
                                            Note user: {{ $user->username }}
                                        </h3>
                                        <form
                                            class="dialog__form"
                                            method="POST"
                                            action="{{ route('staff.notes.update', ['note' => $note]) }}"
                                            x-data="formSubmit"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <p class="form__group">
                                                <textarea
                                                    id="message"
                                                    class="form__textarea"
                                                    name="message"
                                                    placeholder=" "
                                                >
{{ $note->message }}</textarea
                                                >
                                                <label
                                                    class="form__label form__label--floating"
                                                    for="message"
                                                >
                                                    Note
                                                </label>
                                            </p>
                                            <p class="form__group">
                                                <button
                                                    class="form__button form__button--filled"
                                                    popovertarget="note-edit-{{ $note->id }}"
                                                >
                                                    {{ __('common.save') }}
                                                </button>
                                                <button
                                                    class="form__button form__button--outlined"
                                                    type="button"
                                                    popovertarget="note-edit-{{ $note->id }}"
                                                >
                                                    {{ __('common.cancel') }}
                                                </button>
                                            </p>
                                        </form>
                                    </dialog>
                                </li>
                                <li class="data-table__action">
                                    <form>
                                        <button
                                            x-on:click.prevent="destroy"
                                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this note: ' . $note->message . '?') }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.delete') }}
                                        </button>
                                    </form>
                                </li>
                            </menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No notes</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('userNote', () => ({
                update() {
                    this.$wire.update(this.$root.dataset.noteId);
                },
                destroy() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: atob(this.$el.dataset.b64DeletionMessage),
                        icon: 'warning',
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$wire.destroy(this.$root.dataset.noteId);
                        }
                    });
                },
            }));
        });
    </script>
</section>
