<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.blocked-ips') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <button class="form__button form__button--outlined" popovertarget="ip-block">
                    {{ __('common.add') }}
                </button>
                <dialog id="ip-block" class="dialog" popover>
                    <h3 class="dialog__heading">Block IP Address</h3>
                    <form class="dialog__form">
                        <p class="form__group">
                            <input
                                id="ipAddress"
                                class="form__text"
                                type="text"
                                autocomplete="off"
                                name="ipAddress"
                                placeholder=" "
                                wire:model="ipAddress"
                            />
                            <label class="form__label form__label--floating" for="ipAddress">
                                Ip address
                            </label>
                        </p>
                        <p class="form__group">
                            <textarea
                                id="reason"
                                class="form__textarea"
                                name="reason"
                                placeholder=" "
                                wire:model="reason"
                            ></textarea>
                            <label class="form__label form__label--floating" for="reason">
                                Reason
                            </label>
                        </p>
                        <p class="form__group">
                            <button
                                class="form__button form__button--filled"
                                wire:click="store"
                                type="button"
                                popovertarget="ip-block"
                            >
                                {{ __('common.save') }}
                            </button>
                            <button
                                class="form__button form__button--outlined"
                                type="button"
                                popovertarget="ip-block"
                            >
                                {{ __('common.cancel') }}
                            </button>
                        </p>
                    </form>
                </dialog>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="ipSearch"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="ipSearch"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="ipSearch">
                        {{ __('common.ip') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="reasonSearch"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="reasonSearch"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="reasonSearch">
                        {{ __('common.reason') }}
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
                    <th>{{ __('common.user') }}</th>
                    <th>{{ __('common.ip') }}</th>
                    <th>{{ __('common.reason') }}</th>
                    <th>{{ __('common.created_at') }}</th>
                    <th>{{ __('user.expires-on') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
                @forelse ($ipAddresses as $ipAddress)
                    <tr x-data="blockedIp" data-blocked-ip-id="{{ $ipAddress->id }}">
                        <td>{{ $ipAddress->id }}</td>
                        <td>
                            <x-user-tag :anon="false" :user="$ipAddress->user" />
                        </td>
                        <td>{{ $ipAddress->ip_address }}</td>
                        <td>{{ $ipAddress->reason }}</td>
                        <td>
                            <time
                                datetime="{{ $ipAddress->created_at }}"
                                title="{{ $ipAddress->created_at }}"
                            >
                                {{ $ipAddress->created_at }}
                            </time>
                        </td>
                        <td>
                            <time
                                datetime="{{ $ipAddress->expires_at ?? 'Never' }}"
                                title="{{ $ipAddress->expires_at }}"
                            >
                                {{ $ipAddress->expires_at ?? 'Never' }}
                            </time>
                        </td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    <form>
                                        <button
                                            x-on:click.prevent="destroy"
                                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this ip: ' . $ipAddress->ip_address . '?') }}"
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
                        <td colspan="7">No blocked ip addresses</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $ipAddresses->links('partials.pagination') }}
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('blockedIp', () => ({
                destroy() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: atob(this.$el.dataset.b64DeletionMessage),
                        icon: 'warning',
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$wire.destroy(this.$root.dataset.blockedIpId);
                        }
                    });
                },
            }));
        });
    </script>
</section>
