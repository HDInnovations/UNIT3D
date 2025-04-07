<section class="panelV2" x-data="tabs" data-default-tab="recommendations" id="tab_wrapper">
    <header class="panel__header">
        <h2 class="panel__heading">Relations</h2>
        <div class="panel__actions" x-data="posterRow">
            <div class="panel__action">
                <button class="form__standard-icon-button" x-bind="scrollLeft">
                    <i class="{{ \config('other.font-awesome') }} fa-angle-left"></i>
                </button>
            </div>
            <div class="panel__action">
                <button class="form__standard-icon-button" x-bind="scrollRight">
                    <i class="{{ \config('other.font-awesome') }} fa-angle-right"></i>
                </button>
            </div>
        </div>
    </header>
    <menu class="panel__tabs">
        <li class="panel__tab" x-bind="tabButton" data-tab="recommendations">Recommendations</li>
        <li class="panel__tab" x-bind="tabButton" data-tab="collection">Collection</li>
        <li class="panel__tab" x-bind="tabButton" data-tab="playlists">Playlists</li>
    </menu>
    <div x-bind="tabPanel" data-tab="recommendations">
        @include('torrent.partials.recommendations')
    </div>
    <div x-bind="tabPanel" data-tab="collection" x-cloak>
        @include('torrent.partials.collection')
    </div>
    <div x-bind="tabPanel" data-tab="playlists" x-cloak>
        @include('torrent.partials.playlists')
    </div>
</section>
