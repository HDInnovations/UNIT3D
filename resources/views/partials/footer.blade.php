@php $bg = rand(1, 13); $bgchange = $bg.".jpg"; @endphp
<br>
<div id="l-footer" style="background-image: url('/img/footer/{{ $bgchange }}');">
    <div class="container">
        <div class="col-md-2 l-footer-section">
            <h2 class="l-footer-section-title"><span class="text-bold">{{ config('other.title') }}</span></h2>
            <footer>{{ config('other.meta_description') }}</footer>
            <br>
            <i class="{{ config('other.font-awesome') }} fa-tv-retro footer-icon" style="font-size: 90px;"></i>
        </div>

        <div class="col-md-2 l-footer-section">
            <h2 class="l-footer-section-title">@lang('common.account')</h2>
            <ul>
                <li>
                    <a
                        href="{{ route('users.show', ['username' => auth()->user()->username]) }}">@lang('user.my-profile')</a>
                </li>
                <li>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">@lang('common.logout')</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf
                    </form>
                </li>
            </ul>
        </div>

        <div class="col-md-2 l-footer-section">
            <h2 class="l-footer-section-title">@lang('common.community')</h2>
            <ul>
                <li><a href="{{ route('forums.index') }}">@lang('forum.forums')</a></li>
                <li><a href="{{ route('articles.index') }}">@lang('common.news')</a></li>
            </ul>
        </div>

        @if ($footer_pages)
            <div class="col-md-2 l-footer-section">
                <h2 class="l-footer-section-title">@lang('common.pages')</h2>
                <ul>
                    @foreach ($footer_pages as $page)
                        <li><a href="{{ route('pages.show', ['id' => $page->id]) }}">{{ $page->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('pages.index') }}">[View All]</a></li>
                </ul>
            </div>
        @endif

        <div class="col-md-2 l-footer-section">
            <h2 class="l-footer-section-title">@lang('common.info')</h2>
            <ul>
                <li><a href="{{ route('staff') }}">@lang('common.staff')</a></li>
                <li><a href="{{ route('internal') }}">@lang('common.internal')</a></li>
                <li><a href="{{ route('blacklist') }}">@lang('common.blacklist')</a></li>
                <li><a href="{{ route('about') }}">@lang('common.about')</a></li>
            </ul>
        </div>

        <div class="col-md-2 l-footer-section">
            <h2 class="l-footer-section-title">@lang('common.other')</h2>
            <ul>
                <li><a href="https://github.com/sponsors/HDVinnie" target="_blank"
                        class="btn btn-xs btn-primary">@lang('common.sponsor')</a></li>
                <li><a href="https://github.com/HDInnovations/UNIT3D" target="_blank"
                        class="btn btn-xs btn-primary">@lang('common.powered-by')</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="subfooter text-center">
    <div class="container">
        <div class="subfooter-inner">
            <div class="row">
                <div class="col-md-12">
                    <span class="text-bold">
                        This page took {{ \round(\microtime(true) - LARAVEL_START, 3) }} seconds to render
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button">
    <i class="{{ config('other.font-awesome') }} fa-arrow-square-up"></i>
</a>
<a id="back-to-down" href="#" class="btn btn-primary btn-lg back-to-down" role="button">
    <i class="{{ config('other.font-awesome') }} fa-arrow-square-down"></i>
</a>
