@extends('layout.with-main')

@section('title')
    <title>{{ $user->username }} {{ __('user.following') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.following') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-following--index')

@section('main')
    @if (auth()->id() === $user->id || auth()->user()->group->is_modo)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.following') }}</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('user.avatar') }}</th>
                            <th>{{ __('user.user') }}</th>
                            <th>{{ __('common.created_at') }}</th>
                            @if (auth()->id() === $user->id)
                                <th>{{ __('common.actions') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($followings as $following)
                            <tr>
                                <td>
                                    <img
                                        src="{{ $following->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $following]) }}"
                                        alt=""
                                        class="user-search__avatar"
                                    />
                                </td>
                                <td>
                                    <x-user-tag :anon="false" :user="$following" />
                                </td>
                                <td>{{ $following->follow->created_at }}</td>
                                @if (auth()->id() === $user->id)
                                    <td>
                                        <menu class="data-table__actions">
                                            <li class="data-table__action">
                                                <form
                                                    method="POST"
                                                    action="{{ route('users.followers.destroy', ['user' => $following]) }}"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="form__button form__button--text">
                                                        {{ __('user.unfollow') }}
                                                    </button>
                                                </form>
                                            </li>
                                        </menu>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + (int) auth()->id() === $user->id }}">
                                    Not following
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $followings->links('partials.pagination') }}
        </section>
    @else
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.private-profile') }}</h2>
            <div class="panel__body">{{ __('user.not-authorized') }}</div>
        </section>
    @endif
@endsection
