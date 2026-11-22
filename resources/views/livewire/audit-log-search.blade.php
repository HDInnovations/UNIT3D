<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.audit-log') }}</h2>
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
                    <label class="form__label form__label--floating" for="username">Username</label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <select id="model" class="form__select" wire:model.live="modelName">
                        <option selected value="">All</option>
                        @foreach ($modelNames as $modelName)
                            <option value="{{ $modelName }}">{{ $modelName }}</option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="model">Model name</label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="modelId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="modelId"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="modelId">Model ID</label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <select id="action" class="form__select" wire:model.live="action">
                        <option selected value="">All</option>
                        <option value="create">Create</option>
                        <option value="update">Update</option>
                        <option value="delete">Delete</option>
                    </select>
                    <label class="form__label form__label--floating" for="action">Action</label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="record"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="record"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="record">Record</label>
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
                    <th>{{ __('common.action') }}</th>
                    <th>Model</th>
                    <th>Model ID</th>
                    <th>By</th>
                    <th>Changes</th>
                    <th>{{ __('user.created-on') }}</th>
                    <th>{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($audits as $audit)
                    <tr>
                        <td>{{ $audit->id }}</td>
                        <td>
                            <span
                                class="@if($audit->action === 'create') text-green @elseif($audit->action === 'update') text-yellow @elseif($audit->action === 'delete') text-red @endif"
                            >
                                {{ strtoupper($audit->action) }}
                            </span>
                        </td>
                        <td>{{ class_basename($audit->auditable_type) }}</td>
                        <td>{{ $audit->auditable_id }}</td>
                        <td>
                            <a href="{{ route('users.show', ['user' => $audit->user]) }}">
                                {{ $audit->user->username }}
                            </a>
                        </td>
                        <td>
                            <ul>
                                @foreach ($audit->values as $key => $value)
                                    <li
                                        style="
                                            word-wrap: break-word;
                                            word-break: break-word;
                                            overflow-wrap: break-word;
                                        "
                                    >
                                        {{ $key }}:
                                        {{ json_encode($value['old'], JSON_THROW_ON_ERROR) }}
                                        &rarr;
                                        {{ json_encode($value['new'], JSON_THROW_ON_ERROR) }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <time
                                datetime="{{ $audit->created_at }}"
                                title="{{ $audit->created_at }}"
                            >
                                {{ $audit->created_at->diffForHumans() }}
                            </time>
                        </td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    <form
                                        method="POST"
                                        action="{{ route('staff.audits.destroy', ['audit' => $audit]) }}"
                                        x-data="confirmation"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            x-on:click.prevent="confirmAction"
                                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this audit log entry?') }}"
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
                        <td colspan="8">No audits</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $audits->links('partials.pagination') }}
</section>
