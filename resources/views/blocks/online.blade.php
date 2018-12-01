<div class="clearfix visible-sm-block"></div>
<div class="panel panel-chat shoutbox">
    <div class="panel-heading">
        <h4>{{ trans('blocks.users-online') }}
            <small> ({{ trans('blocks.active-in-last') }} 15 min)</small>
        </h4>
    </div>
    <div class="panel-body">
        @foreach($users as $user)
            @if($user->isOnline())
                @if (!$user->hidden)
                    <a href="{{ route('profile', ['username' => $user->username, 'id' => $user->id]) }}">
                        <span class="badge-user text-bold"
                              style="color:{{ $user->group->color }}; background-image:{{ $user->group->effect }}; margin-bottom: 10px;">
                            <i class="{{ $user->group->icon }}" data-toggle="tooltip"
                               data-original-title="{{ $user->group->name }}"></i>
                            {{ $user->username }}
                            @if ($user->getWarning() > 0)
                                <i class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange"
                                   aria-hidden="true"
                                   data-toggle="tooltip"
                                   data-original-title="{{ trans('common.active-warning') }}"></i>
                            @endif
                        </span>
                    </a>
                @endif
            @endif
        @endforeach
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-12">
                <div class="text-center">
                        <span class="badge-user text-orange text-bold">
                            <i class="{{ config('other.font-awesome') }} fa-eye-slash"
                               aria-hidden="true"></i> {{ strtoupper(trans('common.hidden')) }}
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
