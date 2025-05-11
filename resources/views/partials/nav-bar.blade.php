<li class="{{ Route::is('torrents.index') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a class="{{ Route::is('torrents.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}" href="{{ route('torrents.index') }}">
        {{ __('common.search') }}
    </a>
</li>
<li class="{{ Route::is('top10.index') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a class="{{ Route::is('top10.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}" href="{{ route('top10.index') }}">
        {{ __('common.top-10') }}
    </a>
</li>
<li class="{{ Route::is('rss.index') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a class="{{ Route::is('rss.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}" href="{{ route('rss.index') }}">
        {{ __('rss.rss') }}
    </a>
</li>
<li class="{{ Route::is('torrents.create') ? 'nav-tab--active' : 'nav-tabV2' }}">
    <a class="{{ Route::is('torrents.create') ? 'nav-tab--active__link' : 'nav-tab__link' }}" href="{{ route('torrents.create') }}">
        {{ __('common.upload') }}
    </a>
</li>