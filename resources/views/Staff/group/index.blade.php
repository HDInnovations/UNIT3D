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
            <h2 class="panel__heading">{{ __('staff.groups') }}</h2>
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
                        <th>{{ __('staff-interface.id') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.position') }}</th>
                        <th>{{ __('staff-interface.group-level') }}</th>
                        <th>{{ __('staff-interface.group-dl-slots') }}</th>
                        <th>{{ __('common.color') }}</th>
                        <th>{{ __('common.icon') }}</th>
                        <th>{{ __('staff-interface.group-effect') }}</th>
                        <th>{{ __('staff-interface.group-uploader') }}</th>
                        <th>{{ __('common.internal') }}</th>
                        <th>{{ __('staff-interface.group-editor') }}</th>
                        <th>{{ __('staff-interface.group-torrent-modo') }}</th>
                        <th>{{ __('staff-interface.group-modo') }}</th>
                        <th>{{ __('staff-interface.group-admin') }}</th>
                        <th>{{ __('staff-interface.group-owner') }}</th>
                        <th>{{ __('staff-interface.group-trusted') }}</th>
                        <th>{{ __('staff-interface.group-immune') }}</th>
                        <th>{{ __('torrent.freeleech') }}</th>
                        <th>{{ __('torrent.double-upload') }}</th>
                        <th>{{ __('torrent.refundable') }}</th>
                        <th>{{ __('staff-interface.group-incognito') }}</th>
                        <th>{{ __('common.chat') }}</th>
                        <th>{{ __('common.comment') }}</th>
                        <th>{{ __('staff-interface.group-invite') }}</th>
                        <th>{{ __('staff-interface.group-request') }}</th>
                        <th>{{ __('staff-interface.group-can-upload') }}</th>
                        <th>{{ __('staff-interface.group-autogroup') }}</th>
                        <th>{{ __('staff-interface.group-min-uploaded-index') }}</th>
                        <th>{{ __('staff-interface.group-min-ratio-index') }}</th>
                        <th>{{ __('staff-interface.group-min-age-index') }}</th>
                        <th>{{ __('staff-interface.group-min-avg-seedtime-index') }}</th>
                        <th>{{ __('staff-interface.group-min-seedsize-index') }}</th>
                        <th>{{ __('staff-interface.group-min-uploads-index') }}</th>
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
                            <td>
                                {{ $group->download_slots ?? __('staff-interface.unlimited') }}
                            </td>
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
                                                    data-b64-deletion-message="{{ base64_encode(__('staff-interface.delete-group-confirm', ['name' => $group->name])) }}"
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
