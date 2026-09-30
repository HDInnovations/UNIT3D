<div class="panelV2" x-data="toggle">
    <h2 class="panel__heading" style="cursor: pointer" x-on:click="toggle">
        <i class="{{ config('other.font-awesome') }} fa-clipboard-list"></i>
        {{ __('media-interface.torrent.audits-heading') }}
        <i
            class="{{ config('other.font-awesome') }} fa-plus-circle fa-pull-right"
            x-show="isToggledOff"
        ></i>
        <i
            class="{{ config('other.font-awesome') }} fa-minus-circle fa-pull-right"
            x-show="isToggledOn"
            x-cloak
        ></i>
    </h2>
    <div class="data-table-wrapper" x-show="isToggledOn" x-cloak>
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('vltava.torrent.user') }}</th>
                    <th>{{ __('common.action') }}</th>
                    <th>{{ __('vltava.torrent.date') }}</th>
                    <th>{{ __('vltava.torrent.modifications') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($torrent->audits as $audit)
                    {{-- format-ignore-start --}}
                    @php $values = json_decode($audit->record, true); @endphp
                    {{-- format-ignore-end --}}
                    <tr>
                        <td>
                            <x-user-tag :user="$audit->user" :anon="false" />
                        </td>
                        <td>{{ $audit->action }}</td>
                        <td>
                            <time
                                datetime="{{ $audit->created_at }}"
                                title="{{ $audit->created_at }}"
                            >
                                {{ $audit->created_at }}
                                ({{ $audit->created_at->diffForHumans() }})
                            </time>
                        </td>
                        <td>
                            <ul>
                                @foreach ($values as $key => $value)
                                    <li
                                        style="
                                            word-wrap: break-word;
                                            word-break: break-word;
                                            overflow-wrap: break-word;
                                        "
                                    >
                                        {{ $key }}:
                                        @if (is_array($value['old']))
                                            {{ Js::from($value['old']) }}
                                        @else
                                            {{ $value['old'] ?? 'null' }}
                                        @endif
                                        &rarr;
                                        @if (is_array($value['new']))
                                            {{ Js::from($value['new']) }}
                                        @else
                                            {{ $value['new'] ?? 'null' }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
