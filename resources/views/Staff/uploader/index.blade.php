@extends('layout.with-main')

@section('title')
    <title>
        {{ __('torrent.uploader') }} {{ __('common.stats') }} - {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('torrent.uploader') }} {{ __('common.stats') }}</li>
@endsection

@section('page', 'page__staff-uploader-index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('torrent.uploader') }} {{ __('common.stats') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('user.total-uploads') }}</th>
                        <th>{{ __('user.uploads') }} {{ __('stat.last60days') }}</th>
                        <th>{{ __('user.total-personal-releases') }}</th>
                        <th>{{ __('user.personal-releases') }} {{ __('stat.last60days') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($uploaders as $uploader)
                        <tr>
                            <td>
                                <x-user-tag :anon="false" :user="$uploader" />
                            </td>
                            <td>{{ $uploader->total_uploads }}</td>
                            <td>{{ $uploader->recent_uploads }}</td>
                            <td>{{ $uploader->total_personal_releases }}</td>
                            <td>{{ $uploader->recent_personal_releases }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
