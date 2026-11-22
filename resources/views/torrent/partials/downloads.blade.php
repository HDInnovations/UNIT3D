<section class="panelV2" x-data="toggle">
    <h2 class="panel__heading" style="cursor: pointer" x-on:click="toggle">
        <i class="{{ config('other.font-awesome') }} fa-clipboard-list"></i>
        Torrent file downloads ({{ $torrent->downloads_count }} total)
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
                    <th>User</th>
                    <th>Downloaded at</th>
                    <th>Client</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($torrent->downloads as $download)
                    <tr>
                        <td>
                            <x-user-tag :user="$download->user" :anon="false" />
                        </td>
                        <td>
                            <time
                                datetime="{{ $download->created_at }}"
                                title="{{ $download->created_at }}"
                            >
                                {{ $download->created_at }}
                                ({{ $download->created_at->diffForHumans() }})
                            </time>
                        </td>
                        <td>{{ $download->type }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
