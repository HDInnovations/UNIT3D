<li class="{{ Route::is('groups') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('groups') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('groups') }}"
    >
        {{ __('common.groups') }}
    </a>
</li>
<li class="{{ Route::is('groups_requirements') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('groups_requirements') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('groups_requirements') }}"
    >
        {{ __('interface.group-requirements') }}
    </a>
</li>
