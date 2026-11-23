@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('event.giveaways') }}
    </li>
@endsection

@section('page', 'page__giveaway--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('event.giveaways') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.starts-at') }}</th>
                        <th>{{ __('common.ends-at') }}</th>
                        <th>{{ __('common.active') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($giveaways as $giveaway)
                        <tr>
                            <td>
                                <a href="{{ route('giveaways.show', ['giveaway' => $giveaway]) }}">
                                    {{ $giveaway->name }}
                                </a>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $giveaway->starts_at }}"
                                    title="{{ $giveaway->starts_at }}"
                                >
                                    {{ $giveaway->starts_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $giveaway->ends_at }}"
                                    title="{{ $giveaway->ends_at }}"
                                >
                                    {{ $giveaway->ends_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                @if ($giveaway->active)
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
