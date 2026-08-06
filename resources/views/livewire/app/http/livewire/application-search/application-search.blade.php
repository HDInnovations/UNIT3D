<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.applications') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <select id="status" class="form__select" wire:model.live="status">
                        <option selected value="">All</option>
                        <option value="1">Approved</option>
                        <option value="0">Pending</option>
                        <option value="2">Rejected</option>
                    </select>
                    <label class="form__label form__label--floating" for="status">Status</label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="receiver"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="email"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="receiver">
                        {{ __('common.email') }}
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
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('common.user') }}</th>
                    <th>{{ __('common.email') }}</th>
                    <th>{{ __('staff.application-type') }}</th>
                    <th>{{ __('common.image') }}</th>
                    <th>{{ __('staff.links') }}</th>
                    <th>{{ __('common.created_at') }}</th>
                    <th>{{ __('common.status') }}</th>
                    <th>{{ __('common.moderated-by') }}</th>
                    <th>{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr x-data="application" data-application-id="{{ $application->id }}">
                        <td>{{ $application->id }}</td>
                        <td>
                            @if ($application->user === null)
                                N/A
                            @else
                                <x-user-tag :anon="false" :user="$application->user" />
                            @endif
                        </td>
                        <td>{{ $application->email }}</td>
                        <td>{{ $application->type }}</td>
                        <td>{{ $application->imageProofs->count() }}</td>
                        <td>{{ $application->urlProofs->count() }}</td>
                        <td>
                            <time
                                datetime="{{ $application->created_at }}"
                                title="{{ $application->created_at }}"
                            >
                                {{ $application->created_at->diffForHumans() }}
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
                                <li class="data-table__action">
                                    <form>
                                        <button
                                            x-on:click.prevent="destroy"
                                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this application from: ' . $application->email . '?') }}"
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
                    <tr class="applications--empty">
                        <td colspan="10">{{ __('common.no') }} {{ __('staff.applications') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $applications->links('partials.pagination') }}
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('application', () => ({
                destroy() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: atob(this.$el.dataset.b64DeletionMessage),
                        icon: 'warning',
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$wire.destroy(this.$root.dataset.applicationId);
                        }
                    });
                },
            }));
        });
    </script>
</section>
