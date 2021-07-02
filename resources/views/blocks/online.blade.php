<div class="col-md-10 col-sm-10 col-md-offset-1">
    <div class="clearfix visible-sm-block"></div>
    <div class="panel panel-chat shoutbox">
        <div class="panel-heading">
            <h4><i class="{{ config('other.font-awesome') }} fa-users"></i> @lang('blocks.users-online')
                <span class="label label-default">{{ $users->count() }}</span>
            </h4>
        </div>

        <div class="panel-body">
            @foreach ($users as $user)
                @if ($user->hidden == 1 || !$user->isVisible($user, 'other', 'show_online'))
                    <span class="badge-user text-orange text-bold" style="margin-bottom: 10px;">
                        <i class="{{ config('other.font-awesome') }} fa-user-ninja"></i>
                        {{ strtoupper(trans('common.hidden')) }}
                        @if (auth()->user()->hasRole('moderator'))
                            <a href="{{ route('users.show', ['username' => $user->username]) }}">
                                {{ $user->username }}
                                @if ($user->warnings_count > 0)
                                    <i class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange" aria-hidden="true"
                                        data-toggle="tooltip" data-original-title="@lang('common.active-warning')"></i>
                                @endif
                            </a>
                        @endif
                    </span>
                @else
                    <a href="{{ route('users.show', ['username' => $user->username]) }}">
                        <span class="badge-user text-bold"
                            style="color:{{ \App\Models\Role::find(auth()->user()->role_id)->color }}; background-image:{{ \App\Models\Role::find(auth()->user()->role_id)->effects }}; margin-bottom: 10px;">
                            <i class="{{ \App\Models\Role::find(auth()->user()->role_id)->icon }}" data-toggle="tooltip"
                                data-original-title="{{ \App\Models\Role::find(auth()->user()->role_id)->name }}"></i>
                            {{ $user->username }}
                            @if ($user->warnings_count > 0)
                                <i class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange" aria-hidden="true"
                                    data-toggle="tooltip" data-original-title="@lang('common.active-warning')"></i>
                            @endif
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12">
                    <div class="text-center">
                        <span class="badge-user text-orange text-bold">
                            <i class="{{ config('other.font-awesome') }} fa-eye-slash" aria-hidden="true"></i>
                            {{ strtoupper(trans('common.hidden')) }}
                        </span>
                        @foreach ($groups as $group)
                            <span class="badge-user text-bold"
                                style="color:{{ $group->color }}; background-image:{{ $group->effect }};">
                                <i class="{{ $group->icon }}" aria-hidden="true"></i> {{ $group->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
