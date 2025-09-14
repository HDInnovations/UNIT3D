@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.groups') }}
    </li>
@endsection

@section('page', 'page__staff-group--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Groups</h2>
            <div class="panel__actions">
                <a
                    href="{{ route('staff.groups.create') }}"
                    class="panel__action form__button form__button--text"
                >
                    {{ __('common.add') }}
                </a>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.position') }}</th>
                        <th>Level</th>
                        <th>DL Slots</th>
                        <th>Color</th>
                        <th>Icon</th>
                        <th>Effect</th>
                        <th>Uploader</th>
                        <th>Internal</th>
                        <th>Editor</th>
                        <th>Torrent Modo</th>
                        <th>Modo</th>
                        <th>Admin</th>
                        <th>Owner</th>
                        <th>Trusted</th>
                        <th>Immune</th>
                        <th>Freeleech</th>
                        <th>Double Upload</th>
                        <th>Refundable</th>
                        <th>Incognito</th>
                        <th>Chat</th>
                        <th>Comment</th>
                        <th>Invite</th>
                        <th>Request</th>
                        <th>Upload</th>
                        <th>Autogroup</th>
                        <th>Min Upload</th>
                        <th>Min Ratio</th>
                        <th>Min Age</th>
                        <th>Min Avg Seedtime</th>
                        <th>Min Seedsize</th>
                        <th>Min Uploads</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groups as $group)
                        <tr>
                            <td>{{ $group->id }}</td>
                            <td>
                                <a href="{{ route('staff.groups.edit', ['group' => $group]) }}">
                                    {{ $group->name }}
                                </a>
                            </td>
                            <td>{{ $group->position }}</td>
                            <td>{{ $group->level }}</td>
                            <td>{{ $group->download_slots ?? 'Unlimited' }}</td>
                            <td>
                                <i
                                    class="{{ config('other.font-awesome') }} fa-circle"
                                    style="color: {{ $group->color }}"
                                ></i>
                                {{ $group->color }}
                            </td>
                            <td>
                                <i class="{{ $group->icon }}"></i>
                                [{{ $group->icon }}]
                            </td>
                            <td>
                                @if ($group->effect !== '' && $group->effect !== 'none')
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_uploader)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_internal)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_editor)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_torrent_modo)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_modo)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_admin)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_owner)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_trusted)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_immune)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_freeleech)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_double_upload)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_refundable)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->is_incognito)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->can_chat)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->can_comment)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->can_invite)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->can_request)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->can_upload)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($group->autogroup)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>

                            @if ($group->autogroup)
                                <td>
                                    {{ \App\Helpers\StringHelper::formatBytes($group->min_uploaded ?? 0) }}
                                </td>
                                <td>{{ $group->min_ratio }}</td>
                                <td>
                                    {{ \App\Helpers\StringHelper::timeElapsed($group->min_age ?? 0) }}
                                </td>
                                <td>
                                    {{ \App\Helpers\StringHelper::timeElapsed($group->min_avg_seedtime ?? 0) }}
                                </td>
                                <td>
                                    {{ \App\Helpers\StringHelper::formatBytes($group->min_seedsize ?? 0) }}
                                </td>
                                <td>{{ $group->min_uploads }}</td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.groups.edit', ['group' => $group]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    @unless ($group->system_required)
                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('staff.groups.destroy', ['group' => $group]) }}"
                                                method="POST"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    x-on:click.prevent="confirmAction"
                                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this group: ' . $group->name . '? All users in this group will be moved to their appropriate groups.') }}"
                                                    class="form__button form__button--text"
                                                >
                                                    {{ __('common.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    @endunless
                                </menu>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
