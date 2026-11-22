<section class="panelV2 blocks__online">
    <h2 class="panel__heading">
        <i class="{{ config('other.font-awesome') }} fa-users"></i>
        {{ __('blocks.users-online') }} ({{ $users->count() }})
    </h2>
    <div class="panel__body">
        <ul style="column-width: 200px; column-gap: 1rem; list-style-type: none; padding: 0">
            @foreach ($users as $user)
                <li>
                    <x-user-tag
                        :user="$user"
                        :anon="$user->privacy?->hidden || ! $user->isVisible($user, 'other', 'show_online')"
                    >
                        @if ($user->warnings_count > 0)
                            <x-slot:appended-icons>
                                <i
                                    class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange"
                                    title="{{ __('common.active-warning') }} ({{ $user->warnings_count }})"
                                ></i>
                            </x-slot>
                        @endif
                    </x-user-tag>
                </li>
            @endforeach
        </ul>
        <hr />
        <ul style="column-width: 200px; column-gap: 1rem; list-style-type: none; padding: 0">
            @foreach ($groups as $group)
                <span class="user-tag" style="padding: 4px 8px; display: block">
                    <span
                        class="user-tag__link {{ $group->icon }}"
                        style="color: {{ $group->color }}"
                        title="{{ $group->name }}"
                    >
                        {{ $group->name }}
                    </span>
                </span>
            @endforeach
        </ul>
    </div>
</section>
