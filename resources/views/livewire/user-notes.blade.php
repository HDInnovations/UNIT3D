<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.user-notes') }}</h2>
        <div class="panel__actions">
            <div class="panel__action" x-data="dialogLivewire">
                <button class="form__button form__button--text" x-bind="showDialog">
                    {{ __('common.add') }}
                </button>
                <dialog class="dialog" x-bind="dialogElement">
                    <h3 class="dialog__heading">
                        {{ __('livewire-interface.note-user', ['username' => $user->username]) }}
                    </h3>
                    <form class="dialog__form" x-bind="dialogForm">
                        <p class="form__group">
                            <textarea
                                id="message"
                                class="form__textarea"
                                name="message"
                                placeholder=" "
                                wire:model="message"
                            ></textarea>
                            <label class="form__label form__label--floating" for="message">
                                {{ __('user.note') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <button
                                class="form__button form__button--filled"
                                wire:click="store"
                                x-bind="submitDialogForm"
                            >
                                {{ __('common.save') }}
                            </button>
                            <button
                                formmethod="dialog"
                                formnovalidate
                                class="form__button form__button--outlined"
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
                                <li
                                    class="data-table__action"
                                    x-data="dialogLivewire"
                                    data-note-id="{{ $note->id }}"
                                    vv
                                >
                                    <button
                                        class="form__button form__button--text"
                                        x-bind="showDialog"
                                    >
                                        {{ __('common.edit') }}
                                    </button>
                                    <dialog class="dialog" x-bind="dialogElement">
                                        <h3 class="dialog__heading">
                                            {{ __('livewire-interface.note-user', ['username' => $user->username]) }}
                                        </h3>
                                        <form class="dialog__form" x-bind="dialogForm">
                                            <p class="form__group">
                                                <textarea
                                                    id="message"
                                                    class="form__textarea"
                                                    name="message"
                                                    placeholder=" "
                                                    wire:model="messages.{{ $note->id }}"
                                                    value="{{ $note->message }}"
                                                ></textarea>
                                                <label
                                                    class="form__label form__label--floating"
                                                    for="message"
                                                >
                                                    {{ __('user.note') }}
                                                </label>
                                            </p>
                                            <p class="form__group">
                                                <button
                                                    class="form__button form__button--filled"
                                                    x-on:click="update"
                                                    x-bind="submitDialogForm"
                                                >
                                                    {{ __('common.save') }}
                                                </button>
                                                <button
                                                    formmethod="dialog"
                                                    formnovalidate
                                                    class="form__button form__button--outlined"
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
                                            data-b64-deletion-message="{{ base64_encode(__('livewire-interface.confirm-delete-note', ['message' => $note->message])) }}"
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
                        <td colspan="5">{{ __('livewire-interface.no-notes') }}</td>
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
                        title: @js(__('livewire-interface.are-you-sure')),
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
