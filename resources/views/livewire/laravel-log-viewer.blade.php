@section('title')
    <title>
        {{ __('livewire-interface.laravel-log-viewer') }} - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('livewire-interface.laravel-log-viewer') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('livewire-interface.laravel-log-viewer') }}</li>
@endsection

@section('page', 'page__staff-laravel-log--index')

<div
    style="
        display: grid;
        grid-template-columns: minmax(0, 1fr) 225px;
        gap: 12px;
        align-items: flex-start;
    "
>
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-list"></i>
                {{ __('livewire-interface.laravel-log-viewer') }}
            </h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <button class="form__button form__button--text" wire:click="clearLatestLog">
                        {{ __('livewire-interface.clear-latest-log') }}
                    </button>
                </div>
                <div class="panel__action">
                    <button class="form__button form__button--text" wire:click="deleteAllLogs">
                        {{ __('livewire-interface.delete-all-logs') }}
                    </button>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.date') }}</th>
                        <th>{{ __('livewire-interface.level') }}</th>
                        <th>{{ __('common.message') }}</th>
                        <th>{{ __('livewire-interface.exception') }}</th>
                        <th>{{ __('livewire-interface.in') }}</th>
                        <th>{{ __('livewire-interface.line') }}</th>
                        <th>{{ __('livewire-interface.count') }}</th>
                    </tr>
                </thead>
                @forelse ($entries as $message => $groupedEntry)
                    <tbody x-data="toggle" style="border-top: 0">
                        <tr x-on:click="toggle" style="cursor: pointer">
                            <td>{{ $groupedEntry[0]['date'] }}</td>
                            <td>
                                @switch($groupedEntry[0]['level'])
                                    @case('CRITICAL')
                                        <span class="text-danger">
                                            {{ $groupedEntry[0]['level'] }}
                                        </span>

                                        @break
                                    @case('ERROR')
                                        <span class="text-warning">
                                            {{ $groupedEntry[0]['level'] }}
                                        </span>

                                        @break
                                    @case('INFO')
                                        <span class="text-info">
                                            {{ $groupedEntry[0]['level'] }}
                                        </span>

                                        @break
                                    @case('WARNING')
                                        <span class="text-info">
                                            {{ $groupedEntry[0]['level'] }}
                                        </span>

                                        @break
                                    @default
                                        {{ $groupedEntry[0]['level'] }}
                                @endswitch
                            </td>
                            <td>{{ $groupedEntry[0]['message'] }}</td>
                            <td>{{ $groupedEntry[0]['exception'] }}</td>
                            <td>{{ $groupedEntry[0]['in'] }}</td>
                            <td>{{ $groupedEntry[0]['line'] }}</td>
                            <td>{{ count($groupedEntry) }}</td>
                        </tr>
                        <tr x-cloak x-show="isToggledOn">
                            <td colspan="7" style="padding: 0 0 0 8px">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('common.date') }}</th>
                                            <th>{{ __('livewire-interface.environment') }}</th>
                                            <th>{{ __('livewire-interface.stacktrace') }}</th>
                                        </tr>
                                    </thead>
                                    @foreach ($groupedEntry as $entry)
                                        <tbody x-data="toggle" style="border-top: 0">
                                            <tr x-on:click="toggle" style="cursor: pointer">
                                                <td>{{ $entry['date'] }}</td>
                                                <td>{{ $entry['env'] }}</td>
                                                <td>
                                                    <button
                                                        class="form__button form__button--text"
                                                        x-on:click.stop="navigator.clipboard.writeText($refs.stacktrace.textContent)"
                                                    >
                                                        {{ __('livewire-interface.copy') }}
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr x-cloak x-show="isToggledOn">
                                                <td colspan="2">
                                                    <div class="bbcode-rendered">
                                                        <pre><code x-ref="stacktrace">{{ $entry['stacktrace'] }}</code></pre>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="7">{{ __('livewire-interface.no-logs-created-yet') }}</td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>
        @if ($entries->hasMorePages())
            <div class="text-center">
                <button class="form__button form__button--filled" wire:click.prevent="loadMore">
                    {{ __('livewire-interface.load-more-entries') }}
                </button>
            </div>
        @endif
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('livewire-interface.entries') }}</h2>
        <select
            multiple
            wire:model.live="logs"
            style="height: 320px; padding: 8px; border-radius: 4px; width: 100%"
        >
            @foreach ($files as $file)
                <option
                    value="{{ $loop->index }}"
                    style="padding: 6px; border-radius: 4px; cursor: pointer"
                >
                    {{ $file->getFilename() }}
                </option>
            @endforeach
        </select>
    </section>
</div>
