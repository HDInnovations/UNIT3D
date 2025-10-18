<menu class="torrent__buttons form__group--short-horizontal">
    <li class="form__group form__group--short-horizontal">
        @if ($fileExists = Storage::disk('torrent-files')->exists($torrent->file_name))
            @if (config('torrent.download_check_page') == 1)
                <a
                    class="form__button form__button--filled form__button--centered"
                    href="{{ route('download_check', ['id' => $torrent->id]) }}"
                    role="button"
                >
                    <i class="{{ config('other.font-awesome') }} fa-download"></i>
                    {{ __('common.download') }}
                </a>
            @else
                <a
                    class="form__button form__button--filled form__button--centered"
                    href="{{ route('download', ['id' => $torrent->id]) }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-download"></i>
                    {{ __('common.download') }}
                </a>
            @endif
        @elseif (config('torrent.magnet'))
            <a
                href="magnet:?dn={{ $torrent->name }}&xt=urn:btih:{{ bin2hex($torrent->info_hash) }}&as={{ route('torrent.download.rsskey', ['id' => $torrent->id, 'rsskey' => $user->rsskey]) }}&tr={{ route('announce', ['passkey' => $user->passkey]) }}&xl={{ $torrent->size }}"
                class="form__button form__button--filled form__button--centered"
            >
                <i class="{{ config('other.font-awesome') }} fa-magnet"></i>
                {{ __('common.magnet') }}
            </a>
        @endif
    </li>
    @if ($fileExists && $torrent->status === \App\Enums\ModerationStatus::APPROVED)
        @if ($torrent->free !== 100 && config('other.freeleech') == false && ! $personal_freeleech && $user->group->is_freeleech == 0 && ! $torrent->freeleechToken_exists)
            <li class="form__group form__group--short-horizontal">
                <form
                    action="{{ route('freeleech_token', ['id' => $torrent->id]) }}"
                    method="POST"
                    style="display: contents"
                    x-data="freeleechTokenConfirmation"
                >
                    @csrf
                    <button
                        class="form__button form__button--outlined form__button--centered"
                        title="{{ __('torrent.fl-tokens-left', ['tokens' => $user->fl_tokens]) }}!"
                        x-on:click.prevent="confirmAction"
                    >
                        {{ __('torrent.use-fl-token') }}
                    </button>
                </form>
                <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('freeleechTokenConfirmation', () => ({
                            confirmAction() {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: 'This will use one of your freeleech tokens!',
                                    icon: 'warning',
                                    showConfirmButton: true,
                                    showCloseButton: true,
                                }).then((result) => {
                                    if (result.isConfirmed && {{ $torrent->seeders }} == 0) {
                                        Swal.fire({
                                            title: 'Are you sure?',
                                            text: 'This torrent has 0 seeders!',
                                            icon: 'warning',
                                            showConfirmButton: true,
                                            showCancelButton: true,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                this.$root.submit();
                                            }
                                        });
                                    } else if (result.isConfirmed) {
                                        this.$root.submit();
                                    }
                                });
                            },
                        }));
                    });
                </script>
            </li>
        @endif
    @endif

    @if ($torrent->status === \App\Enums\ModerationStatus::APPROVED && config('other.thanks-system.is-enabled'))
        <li class="form__group form__group--short-horizontal">
            @livewire('thank-button', ['torrent' => $torrent])
        </li>
    @endif

    @if ($torrent->nfo)
        <li x-data="dialog" class="form__group form__group--short-horizontal">
            <button
                class="form__button form__button--outlined form__button--centered"
                x-bind="showDialog"
            >
                <i class="{{ config('other.font-awesome') }} fa-info-circle"></i>
                NFO
            </button>
            <dialog class="dialog dialog--auto-width" x-bind="dialogElement">
                <h4 class="dialog__heading">NFO</h4>
                <div class="dialog__form" x-bind="dialogForm">
                    <div class="bbcode-rendered" style="text-align: left">
                        <pre
                            style="width: max-content"
                        ><code class="torrent__nfo" style="white-space: pre;">{{ iconv('cp437', 'utf8', $torrent->nfo) }}</code></pre>
                    </div>
                </div>
            </dialog>
        </li>
    @endif

    @if ($torrent->status === \App\Enums\ModerationStatus::APPROVED)
        <li x-data="dialog" class="form__group form__group--short-horizontal">
            <button
                class="form__button form__button--outlined form__button--centered"
                x-bind="showDialog"
            >
                <i class="{{ config('other.font-awesome') }} fa-coins"></i>
                {{ __('torrent.leave-tip') }}
            </button>
            <dialog class="dialog" x-bind="dialogElement">
                <h4 class="dialog__heading">
                    {{ __('torrent.tip-jar') }}
                </h4>
                <form
                    class="dialog__form"
                    method="POST"
                    action="{{ route('users.torrent_tips.store', ['user' => auth()->user()]) }}"
                    x-bind="dialogForm"
                >
                    @csrf
                    <input type="hidden" name="torrent_id" value="{{ $torrent->id }}" />
                    <div>
                        {{ __('torrent.torrent-tips', ['total' => $torrent->total_tips ?? 0, 'user' => $torrent->user_tips ?? 0]) }}.
                        <span>({{ __('torrent.torrent-tips-desc') }})</span>
                    </div>
                    <div class="form__group">
                        <input
                            id="bon"
                            class="form__text"
                            list="torrent_quick_tips"
                            name="bon"
                            placeholder=" "
                            type="text"
                            pattern="[0-9]*"
                            inputmode="numeric"
                        />
                        <label class="form__label form__label--floating" for="bon">
                            {{ __('torrent.define-tip-amount') }}
                        </label>
                        <datalist id="torrent_quick_tips">
                            <option value="1000"></option>
                            <option value="2000"></option>
                            <option value="5000"></option>
                            <option value="10000"></option>
                            <option value="20000"></option>
                            <option value="50000"></option>
                            <option value="100000"></option>
                        </datalist>
                    </div>
                    <div class="form__group">
                        <button class="form__button form__button--filled">
                            {{ __('torrent.leave-tip') }}
                        </button>
                    </div>
                </form>
            </dialog>
        </li>
    @endif

    <li x-data="dialog" class="form__group form__group--short-horizontal">
        <button
            class="form__button form__button--outlined form__button--centered"
            title="{{ __('common.edit') }}"
            x-bind="showDialog"
        >
            <i class="{{ config('other.font-awesome') }} fa-file"></i>
            {{ __('torrent.show-files') }}
        </button>
        <dialog class="dialog dialog--auto-width" x-bind="dialogElement">
            <header class="dialog__header">
                <h4 class="dialog__heading">
                    {{ __('common.files') }}
                </h4>
                @if ($user->group->is_modo)
                    <div class="dialog__actions">
                        <div class="dialog__action">
                            {{ __('torrent.info-hash') }}:
                            {{ bin2hex($torrent->info_hash) }}
                        </div>
                    </div>
                @endif
            </header>
            <div x-bind="dialogForm" x-data="tabs" data-default-tab="hierarchy">
                <menu class="panel__tabs">
                    <li x-bind="tabButton" data-tab="hierarchy">Hierarchy</li>
                    <li x-bind="tabButton" data-tab="list">List</li>
                </menu>
                <div class="dialog__form" x-bind="tabPanel" data-tab="hierarchy" style="gap: 0">
                    @if ($torrent->folder !== null)
                        <span
                            style="
                                display: grid;
                                grid-template-areas: 'icon folder count . size';
                                grid-template-columns: 24px auto auto 1fr auto;
                                align-items: center;
                                padding-bottom: 8px;
                            "
                        >
                            <i
                                class="{{ config('other.font-awesome') }} fa-folder"
                                style="grid-area: icon; padding-right: 4px"
                            ></i>
                            <span style="padding-right: 4px; word-break: break-all">
                                {{ $torrent->folder }}
                            </span>
                            <span style="grid-area: count; padding-right: 4px">
                                ({{ $torrent->files_count }})
                            </span>
                            <span
                                class="text-info"
                                style="grid-area: size; white-space: nowrap; text-align: right"
                                title="{{ $torrent->size }}&nbsp;B"
                            >
                                {{ App\Helpers\StringHelper::formatBytes($torrent->size, 2) }}
                            </span>
                        </span>
                    @endif

                    @each('torrent.partials.file-tree-node', $fileTree, 'node')
                </div>
                <div class="data-table-wrapper" x-bind="tabPanel" data-tab="list">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('common.name') }}</th>
                                <th>{{ __('torrent.size') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($torrent->files as $index => $file)
                                <tr>
                                    <td style="text-align: right">{{ $index + 1 }}</td>
                                    <td style="text-align: left">{{ $file->name }}</td>
                                    <td style="text-align: right" title="{{ $file->size }}&nbsp;B">
                                        {{ $file->getSize() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </dialog>
    </li>
    @if ($torrent->status === \App\Enums\ModerationStatus::APPROVED)
        <li class="form__group form__group--short-horizontal">
            @livewire('bookmark-button', [
            'torrent' => $torrent,
            'isBookmarked' => $torrent->bookmarks_exists,
            'user' => auth()->user(),
            'bookmarksCount' => $torrent->bookmarks_count ?? 0,
        ])
        </li>
    @endif

    @if ($torrent->status === \App\Enums\ModerationStatus::APPROVED && $user->playlists->count() > 0)
        <li x-data="dialog" class="form__group form__group--short-horizontal">
            <button
                class="form__button form__button--outlined form__button--centered"
                x-bind="showDialog"
            >
                <i class="{{ config('other.font-awesome') }} fa-list-ol"></i>
                {{ __('torrent.add-to-playlist') }}
            </button>
            <dialog class="dialog" x-bind="dialogElement">
                <h4 class="dialog__heading">Add torrent to playlist</h4>
                <form
                    class="dialog__form"
                    method="POST"
                    action="{{ route('playlist_torrents.store') }}"
                    x-bind="dialogForm"
                >
                    @csrf
                    <input type="hidden" name="torrent_id" value="{{ $torrent->id }}" />
                    <p class="form__group">
                        <select id="playlist_id" name="playlist_id" class="form__select">
                            @foreach ($user->playlists as $playlist)
                                <option value="{{ $playlist->id }}">{{ $playlist->name }}</option>
                            @endforeach
                        </select>
                        <label for="playlist_id" class="form__label form__label--floating">
                            Your playlists
                        </label>
                    </p>
                    <p class="form__group" style="text-align: left">
                        <button class="form__button form__button--filled">
                            {{ __('common.save') }}
                        </button>
                        <button
                            formmethod="dialog"
                            formnovalidate
                            class="form__button form__button--outlined"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </p>
                </form>
            </dialog>
        </li>
    @endif

    @if ($torrent->status === \App\Enums\ModerationStatus::APPROVED &&
    $torrent->seeders <= 2 &&
    $torrent->history->first() !== null &&
    ! $torrent->history->first()->seeder &&
    $torrent->history->first()->active)
        <li class="form__group form__group--short-horizontal">
            <form
                action="{{ route('reseed', ['id' => $torrent->id]) }}"
                method="POST"
                style="display: contents"
            >
                @csrf
                <button class="form__button form__button--outlined form__button--centered">
                    <i class="{{ config('other.font-awesome') }} fa-envelope"></i>
                    {{ __('torrent.request-reseed') }}
                </button>
            </form>
        </li>
    @endif

    @if ($torrent->resurrections_exists && $torrent->status === \App\Enums\ModerationStatus::APPROVED)
        <li class="form__group form__group--short-horizontal">
            <button class="form__button form__button--outlined form__button--centered" disabled>
                {{ strtolower(__('graveyard.pending')) }}
            </button>
        </li>
    @elseif ($torrent->seeders == 0 && $torrent->created_at->lt(\Illuminate\Support\Carbon::now()->subDays(30)) && $torrent->status === \App\Enums\ModerationStatus::APPROVED)
        <li class="form__group form__group--short-horizontal" x-data="dialog">
            <button
                class="form__button form__button--outlined form__button--centered"
                x-bind="showDialog"
            >
                <i class="{{ config('other.font-awesome') }} fa-list-ol"></i>
                {{ __('graveyard.resurrect') }}
            </button>
            <dialog class="dialog" x-bind="dialogElement">
                <h4 class="dialog__heading">
                    {{ __('graveyard.resurrect') }} {{ strtolower(__('torrent.torrent')) }} ?
                </h4>
                <form
                    class="dialog__form"
                    method="POST"
                    action="{{ route('users.resurrections.store', ['user' => auth()->user()]) }}"
                    x-bind="dialogForm"
                >
                    @csrf
                    <input type="hidden" name="torrent_id" value="{{ $torrent->id }}" />
                    <p>
                        {{
                            __('graveyard.howto-desc', [
                                'currentSeedtime' => $torrent->history->first() === null ? '0' : App\Helpers\StringHelper::timeElapsed($torrent->history->first()->seedtime),
                                'requiredSeedtime' => $torrent->history->first() === null ? App\Helpers\StringHelper::timeElapsed(config('graveyard.time')) : App\Helpers\StringHelper::timeElapsed($torrent->history->first()->seedtime + config('graveyard.time')),
                                'tokens' => config('graveyard.reward'),
                            ])
                        }}
                    </p>
                    <p class="form__group" style="text-align: left">
                        <button class="form__button form__button--filled">
                            {{ __('graveyard.resurrect') }}
                        </button>
                        <button
                            formmethod="dialog"
                            formnovalidate
                            class="form__button form__button--outlined"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </p>
                </form>
            </dialog>
        </li>
    @endif
    @if ($torrent->status === \App\Enums\ModerationStatus::APPROVED)
        <li x-data="dialog" class="form__group form__group--short-horizontal">
            <button
                class="form__button form__button--outlined form__button--centered"
                x-bind="showDialog"
            >
                <i class="{{ config('other.font-awesome') }} fa-fw fa-eye"></i>
                {{ __('common.report') }}
            </button>
            <dialog class="dialog" x-bind="dialogElement">
                <h4 class="dialog__heading">
                    {{ __('common.report') }} {{ strtolower(__('torrent.torrent')) }}:
                    {{ $torrent->name }}
                </h4>
                <form
                    class="dialog__form"
                    method="POST"
                    action="{{ route('report_torrent', ['id' => $torrent->id]) }}"
                    x-bind="dialogForm"
                >
                    @csrf
                    <input type="hidden" name="torrent_id" value="{{ $torrent->id }}" />
                    <p class="form__group">
                        <textarea
                            id="message"
                            class="form__textarea"
                            name="message"
                            required
                        ></textarea>
                        <label
                            for="report_reason"
                            class="form__label form__label--floating"
                            for="message"
                        >
                            {{ __('common.reason') }}
                        </label>
                    </p>
                    <p class="form__group" style="text-align: left">
                        <button class="form__button form__button--filled">
                            {{ __('common.report') }}
                        </button>
                        <button
                            formmethod="dialog"
                            formnovalidate
                            class="form__button form__button--outlined"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </p>
                </form>
            </dialog>
        </li>
    @endif

    @if ($user->group->is_modo)
        @if (! $torrent->trump_exists)
            <li x-data="dialog" class="form__group form__group--short-horizontal">
                <button
                    class="form__button form__button--outlined form__button--centered"
                    x-bind="showDialog"
                >
                    <i class="{{ config('other.font-awesome') }} fa-skull-crossbones"></i>
                    Mark trumpable
                </button>
                <dialog class="dialog" x-bind="dialogElement">
                    <h4 class="dialog__heading">
                        Trump {{ strtolower(__('torrent.torrent')) }}:
                        {{ $torrent->name }}
                    </h4>
                    <form
                        class="dialog__form"
                        method="POST"
                        action="{{ route('torrent.trump.store', ['torrent' => $torrent]) }}"
                        x-bind="dialogForm"
                    >
                        @csrf
                        <input type="hidden" name="torrent_id" value="{{ $torrent->id }}" />
                        <p class="form__group">
                            <textarea
                                id="reason"
                                class="form__textarea"
                                name="reason"
                                required
                            ></textarea>
                            <label
                                for="trump_reason"
                                class="form__label form__label--floating"
                                for="reason"
                            >
                                {{ __('common.reason') }}
                            </label>
                        </p>
                        <p class="form__group" style="text-align: left">
                            <button class="form__button form__button--filled">
                                {{ __('common.save') }}
                            </button>
                            <button
                                formmethod="dialog"
                                formnovalidate
                                class="form__button form__button--outlined"
                            >
                                {{ __('common.cancel') }}
                            </button>
                        </p>
                    </form>
                </dialog>
            </li>
        @else
            <li class="form__group form__group--short-horizontal">
                <form
                    action="{{ route('torrent.trump.destroy', ['torrent' => $torrent]) }}"
                    method="POST"
                    style="display: inline"
                >
                    @csrf
                    @method('DELETE')
                    <button class="form__button form__button--outlined form__button--centered">
                        <i class="{{ config('other.font-awesome') }} fa-skull-crossbones"></i>
                        Unmark trumpable
                    </button>
                </form>
            </li>
        @endif
    @endif
</menu>
