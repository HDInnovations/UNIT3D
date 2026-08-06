<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('user.password-resets') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="username"
                        class="form__text"
                        type="text"
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
                    <select id="quantity" class="form__select" wire:model="perPage" required>
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
                    <th wire:click="sortBy('created_at')" role="columnheader button">
                        {{ __('common.created_at') }}
                        @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                    </th>
                </tr>
                @forelse ($passwordResetHistories as $passwordResetHistory)
                    <tr>
                        <td>
                            <x-user-tag :user="$passwordResetHistory->user" :anon="false" />
                        </td>
                        <td>
                            <time
                                datetime="{{ $passwordResetHistory->created_at }}"
                                title="{{ $passwordResetHistory->created_at }}"
                            >
                                {{ $passwordResetHistory->created_at }}
                            </time>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No password resets</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $passwordResetHistories->links('partials.pagination') }}
</section>
