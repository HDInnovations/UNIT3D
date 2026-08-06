<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.passkeys') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="passkey"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="passkey"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="passkey">
                        {{ __('user.passkey') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="username"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="username"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="username">
                        {{ __('common.username') }}
                    </label>
                </div>
            </div>
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
        </div>
    </header>
    <div class="data-table-wrapper">
        <table class="data-table">
            <tbody>
                <tr>
                    <th wire:click="sortBy('user_id')" role="columnheader button">
                        {{ __('common.username') }}
                        @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                    </th>
                    <th wire:click="sortBy('content')" role="columnheader button">
                        {{ __('user.passkey') }}
                        @include('livewire.includes._sort-icon', ['field' => 'content'])
                    </th>
                    <th wire:click="sortBy('created_at')" role="columnheader button">
                        {{ __('common.created_at') }}
                        @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                    </th>
                    <th wire:click="sortBy('deleted_at')" role="columnheader button">
                        {{ __('user.deleted-on') }}
                        @include('livewire.includes._sort-icon', ['field' => 'deleted_at'])
                    </th>
                </tr>
                @forelse ($passkeys as $passkey)
                    <tr>
                        <td>
                            <x-user-tag :user="$passkey->user" :anon="false" />
                        </td>
                        <td>{{ $passkey->content }}</td>
                        <td>
                            <time
                                datetime="{{ $passkey->created_at }}"
                                title="{{ $passkey->created_at }}"
                            >
                                {{ $passkey->created_at }}
                            </time>
                        </td>
                        <td>
                            <time
                                datetime="{{ $passkey->deleted_at }}"
                                title="{{ $passkey->deleted_at }}"
                            >
                                {{ $passkey->deleted_at ?? 'Currently in use' }}
                            </time>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No passkeys</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $passkeys->links('partials.pagination') }}
</section>
