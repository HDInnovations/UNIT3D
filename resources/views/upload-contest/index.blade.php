@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumb--active">{{ __('common.upload') }} {{ __('common.contests') }}</li>
@endsection

@section('page', 'page__upload-contest--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.upload') }} {{ __('common.contests') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.starts-at') }}</th>
                        <th>{{ __('common.ends-at') }}</th>
                        <th>{{ __('common.active') }}</th>
                        <th>{{ __('common.awarded') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($uploadContests as $uploadContest)
                        <tr>
                            <td>
                                <a href="{{ route('events.show', ['event' => $uploadContest]) }}">
                                    {{ $uploadContest->name }}
                                </a>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $uploadContest->starts_at }}"
                                    title="{{ $uploadContest->starts_at }}"
                                >
                                    {{ $uploadContest->starts_at->startOfDay() }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $uploadContest->ends_at }}"
                                    title="{{ $uploadContest->ends_at }}"
                                >
                                    {{ $uploadContest->ends_at->endOfDay() }}
                                </time>
                            </td>
                            <td>
                                @if ($uploadContest->active)
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
                                @if ($uploadContest->awarded)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
