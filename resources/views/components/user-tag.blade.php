@props([
    'style',
    'anon',
    'appendedIcons',
    'user',
])

@if ($anon)
    @if (auth()->user()->is($user) || auth()->user()->group->is_modo)
        <span
            {{ $attributes->class('user-tag fas fa-eye-slash') }}
            @if ($user->is_donor == 1)
                {{ $attributes->merge(['style' => 'background-image: url(/img/sparkels.gif);' . ($style ?? '')]) }}
            @else
                {{ $attributes->merge(['style' => 'background-image: ' . $user->group->effect . ';' . ($style ?? '')]) }}
            @endif
        >
            (
            <a
                class="user-tag__link user-tag__link--anonymous {{ $user->group->icon }}"
                href="{{ route('users.show', ['user' => $user]) }}"
                style="color: {{ $user->group->color }}"
                title="{{ $user->group->name }}"
            >
                {{ $user->username }}
            </a>
            @if ($user->icon !== null)
                <i>
                    <img
                        @style([
                            'max-height: 22px;' =>
                                request()
                                    ->route()
                                    ->getName() === 'users.show',
                            'max-height: 17px;' =>
                                request()
                                    ->route()
                                    ->getName() !== 'users.show',
                            'vertical-align: text-bottom',
                        ])
                        title="Custom user icon"
                        src="{{ route('authenticated_images.user_icon', ['user' => $user]) }}"
                    />
                </i>
            @endif

            @if ($user->is_lifetime == 1)
                <i class="fal fa-star" id="lifeline" title="Lifetime donor"></i>
            @endif

            @if ($user->is_donor == 1 && $user->is_lifetime == 0)
                <i class="fal fa-star text-gold" title="Donor"></i>
            @endif

            {{ $appendedIcons ?? '' }}
            )
        </span>
    @else
        <span {{ $attributes->class('user-tag fas fa-eye-slash') }}>
            ({{ __('common.anonymous') }})
        </span>
    @endif
@else
    <span
        {{ $attributes->class('user-tag') }}
        @if ($user->is_donor == 1)
            {{ $attributes->merge(['style' => 'background-image: url(/img/sparkels.gif);' . ($style ?? '')]) }}
        @else
            {{ $attributes->merge(['style' => 'background-image: ' . $user->group->effect . ';' . ($style ?? '')]) }}
        @endif
    >
        <a
            class="user-tag__link {{ $user->group->icon }}"
            href="{{ route('users.show', ['user' => $user]) }}"
            style="color: {{ $user->group->color }}"
            title="{{ $user->group->name }}"
        >
            {{ $user->username }}
        </a>
        @if ($user->icon !== null)
            <i>
                <img
                    @style([
                        'max-height: 22px;' =>
                            request()
                                ->route()
                                ->getName() === 'users.show',
                        'max-height: 17px;' =>
                            request()
                                ->route()
                                ->getName() !== 'users.show',
                        'vertical-align: text-bottom',
                    ])
                    title="Custom user icon"
                    src="{{ route('authenticated_images.user_icon', ['user' => $user]) }}"
                />
            </i>
        @endif

        @if ($user->is_lifetime == 1)
            <i class="fal fa-star" id="lifeline" title="Lifetime donor"></i>
        @endif

        @if ($user->is_donor == 1 && $user->is_lifetime == 0)
            <i class="fal fa-star text-gold" title="Donor"></i>
        @endif

        {{ $appendedIcons ?? '' }}
    </span>
@endif
