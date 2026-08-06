<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.user-notes') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <select id="quantity" class="form__select" wire:model.live="perPage" required>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    <label class="form__label form__label--floating" for="quantity">
                        {{ __('common.quantity') }}
                    </label>
                </div>
            </div>
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
                    <label class="form__label form__label--floating" for="search">Message</label>
                </div>
            </div>
        </div>
    </header>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('common.user') }}</th>
                    <th>{{ __('common.staff') }}</th>
                    <th>{{ __('common.message') }}</th>
                    <th>{{ __('user.created-on') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notes as $note)
                    <tr x-data="note" data-note-id="{{ $note->id }}">
                        <td>{{ $note->id }}</td>
                        <td>
                            <x-user-tag :anon="false" :user="$note->user" />
                        </td>
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
                            <menu class="data-table__actions">
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
                        <td colspan="6">No notes</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $notes->links('partials.pagination') }}
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('note', () => ({
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
