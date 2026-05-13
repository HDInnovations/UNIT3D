<div style="display: flex; flex-direction: column; row-gap: 1rem">
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('common.search') }}</h2>
        </header>
        <div class="panel__body" style="padding: 5px">
            <form class="form">
                <div class="form__group--short-horizontal">
                    <p class="form__group">
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
                    </p>
                    <p class="form__group">
                        <input
                            id="soundexUsername"
                            class="form__text"
                            type="text"
                            wire:model.live="soundexUsername"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="soundexUsername">
                            Soundex {{ __('common.username') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="email"
                            class="form__text"
                            type="text"
                            wire:model.live="email"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="email">
                            {{ __('common.email') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="soundexEmail"
                            class="form__text"
                            type="text"
                            wire:model.live="soundexEmail"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="soundexEmail">
                            Soundex {{ __('common.email') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="searchEmailUsers"
                            class="form__checkbox"
                            type="checkbox"
                            wire:model.live="searchEmailUsers"
                        />
                        <label class="form__label" for="searchEmailUsers">
                            {{ __('common.users') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="searchEmailInvites"
                            class="form__checkbox"
                            type="checkbox"
                            wire:model.live="searchEmailInvites"
                        />
                        <label class="form__label" for="searchEmailInvites">
                            {{ __('staff.invites-log') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="searchEmailApplications"
                            class="form__checkbox"
                            type="checkbox"
                            wire:model.live="searchEmailApplications"
                        />
                        <label class="form__label" for="searchEmailApplications">
                            {{ __('staff.applications') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="apikey"
                            class="form__text"
                            type="text"
                            wire:model.live="apikey"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="apikey">
                            {{ __('user.apikey') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="rsskey"
                            class="form__text"
                            type="text"
                            wire:model.live="rsskey"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="rsskey">
                            {{ __('user.rsskey') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="passkey"
                            class="form__text"
                            type="text"
                            wire:model.live="passkey"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="passkey">
                            {{ __('user.passkey') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="groupId"
                            class="form__select"
                            wire:model.live="groupId"
                            x-data="{ groupId: '' }"
                            x-model="groupId"
                            x-bind:class="groupId === '' ? 'form__select--default' : ''"
                        >
                            <option hidden disabled selected value=""></option>
                            @foreach ($groups as $group)
                                <option class="form__option" value="{{ $group->id }}">
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="groupId" class="form__label form__label--floating">
                            {{ __('common.group') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="show"
                            class="form__checkbox"
                            type="checkbox"
                            wire:model.live="show"
                        />
                        <label class="form__label" for="show">Include soft deletes</label>
                    </p>
                    <p class="form__group">
                        <select
                            id="quantity"
                            class="form__select"
                            wire:model.live="perPage"
                            required
                        >
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <label class="form__label form__label--floating" for="quantity">
                            {{ __('common.quantity') }}
                        </label>
                    </p>
                </div>
            </form>
        </div>
    </section>
    @if (! $emailSearchIsActive || $searchEmailUsers)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('common.users') }}</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <tbody>
                        <tr>
                            <th>Avatar</th>
                            <th wire:click="sortBy('username')" role="columnheader button">
                                {{ __('common.username') }}
                                @include('livewire.includes._sort-icon', ['field' => 'username'])
                            </th>
                            <th wire:click="sortBy('group_id')" role="columnheader button">
                                {{ __('common.group') }}
                                @include('livewire.includes._sort-icon', ['field' => 'group_id'])
                            </th>
                            <th wire:click="sortBy('email')" role="columnheader button">
                                {{ __('common.email') }}
                                @include('livewire.includes._sort-icon', ['field' => 'email'])
                            </th>
                            <th wire:click="sortBy('created_at')" role="columnheader button">
                                {{ __('user.registration-date') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                            <th wire:click="sortBy('last_login')" role="columnheader button">
                                {{ __('user.last-login') }}
                                @include('livewire.includes._sort-icon', ['field' => 'last_login'])
                            </th>
                            <th wire:click="sortBy('last_action')" role="columnheader button">
                                {{ __('user.last-action') }}
                                @include('livewire.includes._sort-icon', ['field' => 'last_action'])
                            </th>
                            <th>{{ __('common.action') }}</th>
                        </tr>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <img
                                        src="{{ $user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $user]) }}"
                                        alt=""
                                        class="user-search__avatar"
                                    />
                                </td>
                                <td colspan="2">
                                    <x-user-tag :anon="false" :user="$user" />
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <time
                                        datetime="{{ $user->created_at }}"
                                        title="{{ $user->created_at }}"
                                    >
                                        {{ $user->created_at }}
                                    </time>
                                </td>
                                <td>
                                    <time
                                        datetime="{{ $user->last_login }}"
                                        title="{{ $user->last_login }}"
                                    >
                                        {{ $user->last_login ?? 'Never' }}
                                    </time>
                                </td>
                                <td>
                                    <time
                                        datetime="{{ $user->last_action }}"
                                        title="{{ $user->last_action }}"
                                    >
                                        {{ $user->last_action ?? 'Never' }}
                                    </time>
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                class="form__button form__button--text"
                                                href="{{ route('staff.users.edit', ['user' => $user]) }}"
                                            >
                                                {{ __('common.edit') }}
                                            </a>
                                        </li>
                                    </menu>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No users</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links('partials.pagination') }}
        </section>
    @endif

    @if ($emailSearchIsActive && $searchEmailInvites)
        <section class="panelV2">
            <h2 class="panel__heading">Invite email matches</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('user.sender') }}</th>
                            <th>{{ __('common.email') }}</th>
                            <th>Code</th>
                            <th>{{ __('user.created-on') }}</th>
                            <th>{{ __('user.accepted-by') }}</th>
                            <th>{{ __('user.deleted-on') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invites as $invite)
                            <tr>
                                <td>{{ $invite->id }}</td>
                                <td>
                                    <x-user-tag :anon="false" :user="$invite->sender" />
                                </td>
                                <td>{{ $invite->email }}</td>
                                <td>{{ $invite->code }}</td>
                                <td>
                                    <time
                                        datetime="{{ $invite->created_at }}"
                                        title="{{ $invite->created_at }}"
                                    >
                                        {{ $invite->created_at }}
                                    </time>
                                </td>
                                <td>
                                    @if ($invite->accepted_by === null)
                                        N/A
                                    @else
                                        <x-user-tag :anon="false" :user="$invite->receiver" />
                                    @endif
                                </td>
                                <td>
                                    <time
                                        datetime="{{ $invite->deleted_at }}"
                                        title="{{ $invite->deleted_at }}"
                                    >
                                        {{ $invite->deleted_at ?? 'N/A' }}
                                    </time>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No invite email matches</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $invites->links('partials.pagination') }}
        </section>
    @endif

    @if ($emailSearchIsActive && $searchEmailApplications)
        <section class="panelV2">
            <h2 class="panel__heading">Application email matches</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('common.email') }}</th>
                            <th>{{ __('staff.application-type') }}</th>
                            <th>{{ __('common.created_at') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('common.moderated-by') }}</th>
                            <th>{{ __('common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $application)
                            <tr>
                                <td>{{ $application->id }}</td>
                                <td>{{ $application->email }}</td>
                                <td>{{ $application->type }}</td>
                                <td>
                                    <time
                                        datetime="{{ $application->created_at }}"
                                        title="{{ $application->created_at }}"
                                    >
                                        {{ $application->created_at }}
                                    </time>
                                </td>
                                <td>
                                    @switch($application->status)
                                        @case(\App\Enums\ModerationStatus::PENDING)
                                            <span class="application--pending">Pending</span>

                                            @break
                                        @case(\App\Enums\ModerationStatus::APPROVED)
                                            <span class="application--approved">Approved</span>

                                            @break
                                        @case(\App\Enums\ModerationStatus::REJECTED)
                                            <span class="application--rejected">Rejected</span>

                                            @break
                                        @default
                                            <span class="application--unknown">Unknown</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if ($application->moderated === null)
                                        N/A
                                    @else
                                        <x-user-tag :anon="false" :user="$application->moderated" />
                                    @endif
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                class="form__button form__button--text"
                                                href="{{ route('staff.applications.show', ['id' => $application->id]) }}"
                                            >
                                                {{ __('common.view') }}
                                            </a>
                                        </li>
                                    </menu>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No application email matches</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $applications->links('partials.pagination') }}
        </section>
    @endif
</div>
