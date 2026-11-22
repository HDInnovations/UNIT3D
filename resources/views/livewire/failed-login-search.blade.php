<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.failed-login-log') }}</h2>
        <div class="panel__actions">
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
                    <input
                        id="userId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        wire:model.live="userId"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="userId">
                        {{ __('user.user-id') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="ipAddress"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="ipAddress"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="ipAddress">
                        {{ __('common.ip') }}
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
                    <th>{{ __('common.no') }}</th>
                    <th wire:click="sortBy('username')" role="columnheader button">
                        {{ __('common.username') }}
                        @include('livewire.includes._sort-icon', ['field' => 'username'])
                    </th>
                    <th wire:click="sortBy('user_id')" role="columnheader button">
                        {{ __('user.user-id') }}
                        @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                    </th>
                    <th wire:click="sortBy('ip_address')" role="columnheader button">
                        {{ __('common.ip') }}
                        @include('livewire.includes._sort-icon', ['field' => 'ip_address'])
                    </th>
                    <th wire:click="sortBy('created_at')" role="columnheader button">
                        {{ __('common.created_at') }}
                        @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                    </th>
                </tr>
                @forelse ($failedLogins as $failedLogin)
                    <tr>
                        <td>{{ $failedLogin->id }}</td>
                        <td>
                            @if ($failedLogin->user_id === null)
                                {{ $attempt->username }}
                            @else
                                <x-user-tag :anon="false" :user="$failedLogin->user" />
                            @endif
                        </td>
                        <td>{{ $failedLogin->user_id ?? 'Not found' }}</td>
                        <td>{{ $failedLogin->ip_address }}</td>
                        <td>
                            <time
                                datetime="{{ $failedLogin->created_at }}"
                                title="{{ $failedLogin->created_at }}"
                            >
                                {{ $failedLogin->created_at }}
                            </time>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No failed logins</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $failedLogins->links('partials.pagination') }}
</section>

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">Top 10 failed logins by IP</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <td>{{ __('common.ip') }}</td>
                        <td>Count</td>
                        <td>Most recent</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($failedLoginsTop10Ip as $failedLogin)
                        <tr>
                            <td>{{ $failedLogin->ip_address }}</td>
                            <td>{{ $failedLogin->login_attempts }}</td>
                            <td>
                                <time
                                    datetime="{{ $failedLogin->latest_created_at }}"
                                    title="{{ $failedLogin->latest_created_at }}"
                                >
                                    {{ $failedLogin->latest_created_at }}
                                </time>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No IPs with more than 5 failed logins</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
