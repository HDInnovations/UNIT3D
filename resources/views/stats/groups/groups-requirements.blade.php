@extends('layout.with-main')

@section('title')
    <title>{{ __('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('stats') }}" class="breadcrumb__link">
            {{ __('stat.stats') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('groups') }}" class="breadcrumb__link">
            {{ __('stat.groups') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('common.groups') }} Requirements</li>
@endsection

@section('nav-tabs')
    @include('partials.statsgroupmenu')
@endsection

@section('page', 'page__stats--groups')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.groups') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table stats__groups-table">
                <thead>
                    <tr>
                        <th>{{ __('common.group') }}</th>
                        <th>Requirement</th>
                        <th>Perks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groups as $group)
                        <tr>
                            <td style="min-width: 20%">
                                <span
                                    style="
                                        color: {{ $group->color }};
                                        background-image: {{ $group->effect }};
                                    "
                                >
                                    <i class="{{ $group->icon }}"></i>
                                    {{ $group->name }}
                                </span>
                            </td>
                            <td>
                                @if ($group->autogroup)
                                    <table class="stats__requirements-table">
                                        <thead>
                                            <tr>
                                                <td></td>
                                                <td>Requirement</td>
                                                <td>To Advance</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Min. Upload</td>
                                                <td>
                                                    {{ \App\Helpers\StringHelper::formatBytes($group->min_uploaded ?? 0) }}
                                                </td>
                                                <td>
                                                    @if ($user->uploaded >= $group->min_uploaded ?? 0)
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-check text-green"
                                                        ></i>
                                                    @else
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-x text-red"
                                                        ></i>
                                                        |
                                                        {{ \App\Helpers\StringHelper::formatBytes($group->min_uploaded - $user->uploaded) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Min. Ratio</td>
                                                <td>{{ $group->min_ratio ?? 0 }}</td>
                                                <td>
                                                    @if ($user->ratio >= $group->min_ratio ?? 0)
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-check text-green"
                                                        ></i>
                                                    @else
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-x text-red"
                                                        ></i>
                                                        | {{ $group->min_ratio - $user->ratio }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Min. Account Age</td>
                                                <td>
                                                    @if ($group->min_age > 0)
                                                        {{ \App\Helpers\StringHelper::timeElapsed($group->min_age ?? 0) }}
                                                    @else
                                                        {{ $group->min_age ?? 0 }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($user->created_at->addRealSeconds($group->min_age ?? 0)->isBefore($current))
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-check text-green"
                                                        ></i>
                                                    @else
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-x text-red"
                                                        ></i>
                                                        |
                                                        {{ \App\Helpers\StringHelper::timeElapsed($group->min_age - $user_account_age) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Min. Average Seedtime</td>
                                                <td>
                                                    {{ \App\Helpers\StringHelper::timeElapsed($group->min_avg_seedtime ?? 0) }}
                                                </td>
                                                <td>
                                                    @if ($group->min_avg_seedtime <= $user_avg_seedtime)
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-check text-green"
                                                        ></i>
                                                    @else
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-x text-red"
                                                        ></i>
                                                        |
                                                        {{ \App\Helpers\StringHelper::timeElapsed($group->min_avg_seedtime - $user_avg_seedtime) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Min. Seedsize</td>
                                                <td>
                                                    {{ \App\Helpers\StringHelper::formatBytes($group->min_seedsize ?? 0) }}
                                                </td>
                                                <td>
                                                    @if ($group->min_seedsize <= $user_seed_size)
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-check text-green"
                                                        ></i>
                                                    @else
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-x text-red"
                                                        ></i>
                                                        |
                                                        {{ \App\Helpers\StringHelper::formatBytes($group->min_seedsize - $user_seed_size) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Min. Uploads</td>
                                                <td>
                                                    {{ $group->min_uploads ?? 0 }}
                                                </td>
                                                <td>
                                                    @if ($group->min_uploads <= $user_uploads)
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-check text-green"
                                                        ></i>
                                                    @else
                                                        <i
                                                            class="{{ config('other.font-awesome') }} fa-x text-red"
                                                        ></i>
                                                        |
                                                        {{ $group->min_uploads - $user_uploads }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    {{ $group->description ?? '' }}
                                @endif
                            </td>
                            <td>
                                <table class="stats__perks-table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i
                                                    class="{{ config('other.font-awesome') }} fa-arrow-down-short-wide text-blue"
                                                ></i>
                                                DL Slots: {{ $group->download_slots ?? '∞' }}
                                            </td>
                                        </tr>

                                        @if ($group->can_upload)
                                            <tr>
                                                <td>
                                                    <i
                                                        class="{{ config('other.font-awesome') }} fa-upload text-success"
                                                    ></i>
                                                    {{ __('common.upload') }}
                                                    {{ __('torrent.torrents') }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($group->can_invite && (! config('other.invites_restriced') || (config('other.invites_restriced') && \in_array($group->name, config('other.invite_groups'), true))))
                                            <tr>
                                                <td>
                                                    <i
                                                        class="{{ config('other.font-awesome') }} fa-paper-plane"
                                                    ></i>
                                                    {{ __('user.send-invite') }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($group->is_freeleech)
                                            <tr>
                                                <td>
                                                    <i
                                                        class="{{ config('other.font-awesome') }} fa-star text-gold"
                                                    ></i>
                                                    {{ __('torrent.freeleech') }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($group->is_double_upload)
                                            <tr>
                                                <td>
                                                    <i
                                                        class="fas fa-chevron-double-up torrent-icons__double-upload"
                                                    ></i>
                                                    {{ __('torrent.double-upload') }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($group->is_refundable)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-percentage"></i>
                                                    {{ __('torrent.refundable') }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($group->is_immune)
                                            <tr>
                                                <td>
                                                    <i
                                                        class="{{ config('other.font-awesome') }} fa-syringe"
                                                    ></i>
                                                    Immune to automated HnR warnings
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($group->is_trusted)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-tasks"></i>
                                                    {{ __('staff.torrent-moderation') }} bypass
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
