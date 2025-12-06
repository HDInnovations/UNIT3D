@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('upload_contests.index') }}" class="breadcrumb__link">
            {{ __('common.upload') }} {{ __('common.contests') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $uploadContest->name }}
    </li>
@endsection

@section('page', 'page__upload-contest--show')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $uploadContest->name }}</h2>
        <div class="data-table-wrapper">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('common.user') }}</th>
                            <th>
                                {{ __('torrent.uploaded') }} (Non-{{ __('common.anonymous') }})
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($uploaders as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <x-user-tag
                                        :user="$user->user"
                                        :anon="$user->user->privacy?->private_profile"
                                    />
                                </td>
                                <td>
                                    {{ $user->value }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('common.starts-at') }}</dt>
                <dd>{{ $uploadContest->starts_at->format('Y-m-d') }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('common.ends-at') }}</dt>
                <dd>{{ $uploadContest->ends_at->format('Y-m-d') }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('common.awarded') }}</dt>
                <dd>
                    @if ($uploadContest->awarded)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-times text-red"></i>
                    @endif
                </dd>
            </div>
        </dl>
        <div class="panel__body">
            {{ $uploadContest->description }}
        </div>
    </section>
@endsection
