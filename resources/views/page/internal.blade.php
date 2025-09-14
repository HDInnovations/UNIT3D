@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('common.internal') }}
    </li>
@endsection

@section('page', 'page__internal--index')

@section('main')
    <!-- Internals in Groups -->
    @foreach ($internals as $internal)
        <section class="panelV2">
            <h2 class="panel__heading">
                <i class="{{ $internal->icon === 'none' ? 'fas fa-magic' : $internal->icon }}"></i>
                {{ $internal->name }}
            </h2>
            <div class="panel__body user-card-wrapper">
                @foreach ($internal->users as $user)
                    <a
                        href="{{ route('users.show', ['user' => $user]) }}"
                        class="user-card"
                        style="
                            background-color: {{ $user->group->color }};
                            background-image: {{ $internal->effect }};
                        "
                    >
                        <h3 class="user-card__username">
                            {{ $user->username }}
                        </h3>
                        <i class="fal {{ $user->group->icon }} user-card__icon"></i>
                        @if ($user->title !== null)
                            <p class="user-card__title">
                                {{ __('page.staff-title') }}: {{ $user->title }}
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endforeach
@endsection
