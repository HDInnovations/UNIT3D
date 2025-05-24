<li class="{{ Route::is('uploaded') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('uploaded') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('uploaded') }}"
    >
        {{ __('user.top-uploaders-data') }}
    </a>
</li>
<li class="{{ Route::is('downloaded') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('downloaded') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('downloaded') }}"
    >
        {{ __('user.top-downloaders-data') }}
    </a>
</li>
<li class="{{ Route::is('seeders') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('seeders') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('seeders') }}"
    >
        {{ __('user.top-seeders') }}
    </a>
</li>
<li class="{{ Route::is('leechers') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('leechers') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('leechers') }}"
    >
        {{ __('user.top-leechers') }}
    </a>
</li>
<li class="{{ Route::is('uploaders') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('uploaders') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('uploaders') }}"
    >
        {{ __('user.top-uploaders-count') }}
    </a>
</li>
<li class="{{ Route::is('bankers') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('bankers') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('bankers') }}"
    >
        {{ __('user.top-bankers') }}
    </a>
</li>
<li class="{{ Route::is('seedtime') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('seedtime') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('seedtime') }}"
    >
        {{ __('user.top-seedtime') }}
    </a>
</li>
<li class="{{ Route::is('seedsize') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('seedsize') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('seedsize') }}"
    >
        {{ __('user.top-seeding-size') }}
    </a>
</li>
<li class="{{ Route::is('upload_snatches') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('upload_snatches') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('upload_snatches') }}"
    >
        Most Upload Snatches
    </a>
</li>
<li class="{{ Route::is('messages') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a
        class="{{ Route::is('messages') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('messages') }}"
    >
        Most Messages
    </a>
</li>
