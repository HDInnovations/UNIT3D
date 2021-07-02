<header id="hoe-header" hoe-color-type="header-bg5" hoe-lpanel-effect="shrink" class="hoe-minimized-lpanel">
    <div class="hoe-left-header" hoe-position-type="fixed">
        <a href="{{ route('home.index') }}">
            <div class="banner">
                <i class="fal fa-tv-retro" style="display: inline;"></i>
            </div>
        </a>
        <span class="hoe-sidebar-toggle"><a href="#"></a></span>
    </div>
    <div class="hoe-right-header" hoe-position-type="fixed" hoe-color-type="header-bg5">
        <span class="hoe-sidebar-toggle"><a href="#"></a></span>
        <ul class="left-navbar">
            <li class="dropdown hoe-rheader-submenu message-notification left-min-30">
                @php $pm = DB::table('private_messages')->where('receiver_id', '=', auth()->user()->id)->where('read', '=', '0')->count(); @endphp
                <a href="{{ route('inbox') }}" class="dropdown-toggle icon-circle">
                    <i class="{{ config('other.font-awesome') }} fa-envelope"></i>
                    @if ($pm > 0)
                        <div class="notify"><span class="heartbit"></span><span class="point fa-beat"></span></div>
                    @endif
                </a>
            </li>

            <li class="dropdown hoe-rheader-submenu message-notification left-min-30">
                <a href="{{ route('notifications.index') }}" class="icon-circle">
                    <i class="{{ config('other.font-awesome') }} fa-bell"></i>
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <div class="notify"><span class="heartbit"></span><span class="point fa-beat"></span></div>
                    @endif
                </a>
            </li>

            <li class="dropdown hoe-rheader-submenu message-notification left-min-30">
                <a href="{{ route('articles.index') }}" class="icon-circle">
                    <i class="{{ config('other.font-awesome') }} fa-newspaper"></i>
                </a>
            </li>

            <li class="dropdown hoe-rheader-submenu message-notification left-min-65">
                <a href="{{ route('tickets.index') }}" class="icon-circle">
                    <i class="{{ config('other.font-awesome') }} fa-life-ring"></i>
                    @if (auth()->user()->hasPrivilegeTo('helpdesk_can_handle'))
                        @php $tickets = DB::table('tickets')
                            ->whereNull('closed_at')->whereNull('staff_id')
                            ->orwhere(function($query) {
                                $query->where('staff_id', '=', auth()->user()->id)
                                      ->Where('staff_read', '=', '0');
                            })
                            ->count();
                        @endphp
                        @if ($tickets > 0)
                            <div class="notify"><span class="heartbit"></span><span class="point fa-beat"></span></div>
                        @endif
                    <!-- Notification for Users -->
                    @else
                        @php $ticket_unread = DB::table('tickets')
                            ->where('user_id', '=', auth()->user()->id)
                            ->where('user_read', '=', '0')
                            ->count();
                        @endphp
                        @if ($ticket_unread > 0)
                            <div class="notify"><span class="heartbit"></span><span class="point fa-beat"></span></div>
                        @endif
                    @endif
                </a>
            </li>

            @if (auth()->user()->hasPrivilegeTo('dashboard_can_view'))
                <li class="dropdown hoe-rheader-submenu message-notification left-min-65">
                    <a href="{{ route('staff.moderation.index') }}" class="icon-circle">
                        <i class="{{ config('other.font-awesome') }} fa-tasks"></i>
                    @php $modder = DB::table('torrents')->where('status', '=', '0')->count(); @endphp
                        @if ($modder > 0)
                            <div class="notify"><span class="heartbit"></span><span class="point fa-beat"></span></div>
                        @endif
                    </a>
                </li>
            @endif
        </ul>

        <ul class="right-navbar">
            <li class="dropdown hoe-rheader-submenu message-notification left-min-30 mobile-hide" style="margin-right:10px;">
                <livewire:quick-search-dropdown />
            </li>
            <li class="dropdown hoe-rheader-submenu hoe-header-profile">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span>
                        @if (auth()->user()->image != null)
                            <img src="{{ url('files/img/' . auth()->user()->image) }}" alt="{{ auth()->user()->username }}"
                                class="img-circle">
                        @else
                            <img src="{{ url('img/profile.png') }}" alt="{{ auth()->user()->username }}" class="img-circle">
                        @endif
                    </span>
                    <span><i class=" {{ config('other.font-awesome') }} fa-angle-down"></i></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('users.show', ['username' => auth()->user()->username]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-user"></i> @lang('user.my-profile')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_settings', ['username' => auth()->user()->username]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-cogs"></i> @lang('user.my-settings')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_privacy', ['username' => auth()->user()->username]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-eye"></i> @lang('user.my-privacy')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_security', ['username' => auth()->user()->username]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-shield-alt"></i> @lang('user.my-security')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('achievements.index') }}">
                            <i class="{{ config('other.font-awesome') }} fa-trophy-alt"></i> My @lang('user.achievements')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_uploads', ['username' => auth()->user()->username]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-upload"></i> @lang('user.my-uploads')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_requested', ['username' => auth()->user()->username]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-question"></i> @lang('user.my-requested')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bookmarks.index', ['username' => auth()->user()->username]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-bookmark"></i> @lang('user.my-bookmarks')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wishes.index', ['username' => auth()->user()->username]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-clipboard-list"></i>
                            @lang('user.my-wishlist')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}">
                            <i class="{{ config('other.font-awesome') }} fa-sign-out"></i> @lang('auth.logout')
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</header>
