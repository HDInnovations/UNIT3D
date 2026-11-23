<section class="panelV2 blocks__top-torrents" x-data="tabs($wire.entangle('tab').live)">
    <header class="panel__header">
        <h2 class="panel__heading">
            {{ __('blocks.top-torrents') }}
        </h2>
        <div class="panel__actions">
            <div class="panel__action">
                <button class="form__button form__button--text" wire:click="$refresh">
                    {{ __('pm.refresh') }}
                    <i class="{{ config('other.font-awesome') }} fa-redo"></i>
                </button>
            </div>
        </div>
    </header>
    <menu class="panel__tabs">
        <li x-bind="tabButton" data-tab="newest">
            {{ __('blocks.new-torrents') }}
        </li>
        <li x-bind="tabButton" data-tab="seeded">
            {{ __('torrent.top-seeded') }}
        </li>
        <li x-bind="tabButton" data-tab="leeched">
            {{ __('torrent.top-leeched') }}
        </li>
        <li x-bind="tabButton" data-tab="dying">
            {{ __('torrent.dying-torrents') }}
        </li>
        <li x-bind="tabButton" data-tab="dead">
            {{ __('torrent.dead-torrents') }}
        </li>
    </menu>
    <div class="data-table-wrapper">
        <table class="data-table">
            <tbody>
                @foreach ($torrents as $torrent)
                    <x-torrent.row
                        :$torrent
                        :meta="$torrent->meta"
                        :personalFreeleech="$personal_freeleech"
                    />
                @endforeach
            </tbody>
        </table>
    </div>
</section>
