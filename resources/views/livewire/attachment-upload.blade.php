<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('ticket.attachments') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="attachment"
                        class="form__file"
                        type="file"
                        wire:model.live="attachment"
                        style="display: none"
                    />
                    <label class="form__button form__button--text" for="attachment">
                        {{ __('common.add') }}
                    </label>
                </div>
            </div>
        </div>
    </header>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('common.name') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attachments as $attachment)
                    <tr>
                        <td>{{ $attachment->file_name }}</td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    <form
                                        action="{{ route('tickets.attachment.download', ['ticket' => $ticket, 'attachment' => $attachment]) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        <button class="form__button form__button--text">
                                            {{ __('ticket.download') }}
                                        </button>
                                    </form>
                                </li>
                            </menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">{{ __('ticket.no-attach') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @error('attachment')
        {{ $message }}
    @enderror
</section>
