@section('title')
    <title>
        {{ __('livewire-interface.torrent-trumps') }} - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('livewire-interface.torrent-trumps') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('livewire-interface.torrent-trumps') }}</li>
@endsection

@section('page', 'page__staff-torrent-trump--index')

<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('livewire-interface.torrent-trumps') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="username"
                        wire:model.live="username"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="username">
                        {{ __('common.username') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="torrent"
                        wire:model.live="torrentName"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="torrent">
                        {{ __('torrent.torrent') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <select id="quantity" class="form__select" wire:model.live="perPage" required>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    <label class="form__label form__label--floating" for="quantity">
                        {{ __('common.quantity') }}
                    </label>
                </div>
            </div>
        </div>
    </header>
    <div class="panel__body" wire:loading.block>{{ __('livewire-interface.loading') }}</div>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th wire:click="sortBy('id')" role="columnheader button">
                        {{ __('livewire-interface.id') }}
                        @include('livewire.includes._sort-icon', ['field' => 'id'])
                    </th>
                    <th wire:click="sortBy('user_id')" role="columnheader button">
                        {{ __('user.user') }}
                        @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                    </th>
                    <th wire:click="sortBy('torrent_id')" role="columnheader button">
                        {{ __('torrent.torrent') }}
                        @include('livewire.includes._sort-icon', ['field' => 'torrent_id'])
                    </th>
                    <th wire:click="sortBy('reason')" role="columnheader button">
                        {{ __('common.reason') }}
                        @include('livewire.includes._sort-icon', ['field' => 'reason'])
                    </th>
                    <th wire:click="sortBy('created_at')" role="columnheader button">
                        {{ __('common.created_at') }}
                        @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($torrentTrumps as $torrentTrump)
                    <tr>
                        <td>{{ $torrentTrump->id }}</td>
                        <td>
                            <x-user-tag :user="$torrentTrump->user" :anon="false" />
                        </td>
                        <td>
                            @if ($torrentTrump->torrent->trashed())
                                <a
                                    class="text-danger"
                                    href="{{ route('torrents.show', ['id' => $torrentTrump->torrent->id]) }}"
                                >
                                    {{ $torrentTrump->torrent->name }}
                                </a>
                            @else
                                <a
                                    href="{{ route('torrents.show', ['id' => $torrentTrump->torrent->id]) }}"
                                >
                                    {{ $torrentTrump->torrent->name }}
                                </a>
                            @endif
                        </td>
                        <td>{{ $torrentTrump->reason }}</td>
                        <td>
                            <time
                                datetime="{{ $torrentTrump->created_at }}"
                                title="{{ $torrentTrump->created_at }}"
                            >
                                {{ $torrentTrump->created_at->toDisplayTimezone()->format('Y-m-d') }}
                            </time>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $torrentTrumps->links('partials.pagination') }}
</section>
