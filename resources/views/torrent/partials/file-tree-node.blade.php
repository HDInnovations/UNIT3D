<details style="margin-left: 20px">
    <summary
        @style([
            'padding: 8px;',
            'list-style-position: outside',
            'cursor: pointer' => $node['type'] === 'directory',
            'list-style-type: none' => $node['type'] === 'file',
        ])
    >
        <span
            style="
                display: grid;
                grid-template-areas: 'icon2 folder count . size';
                grid-template-columns: 24px auto auto 1fr auto;
                gap: 4px;
            "
        >
            @if ($node['type'] === 'file')
                <i class="{{ config('other.font-awesome') }} fa-file" style="grid-area: icon2"></i>
                <span style="word-break: break-all">
                    {{ $key }}
                </span>
                <span
                    style="grid-area: size; white-space: nowrap; text-align: right"
                    title="{{ $node['size'] }}&nbsp;B"
                >
                    {{ App\Helpers\StringHelper::formatBytes($node['size'], 2) }}
                </span>
            @else
                <i
                    class="{{ config('other.font-awesome') }} fa-folder"
                    style="grid-area: icon2"
                ></i>
                <span>
                    {{ $key }}
                </span>

                <span style="grid-area: count">({{ $node['count'] }})</span>
                <span
                    class="text-info"
                    style="grid-area: size; white-space: nowrap; text-align: right"
                    title="{{ $node['size'] }}&nbsp;B"
                >
                    {{ App\Helpers\StringHelper::formatBytes($node['size'], 2) }}
                </span>
            @endif
        </span>
    </summary>
    @if ($node['type'] === 'directory')
        @each('torrent.partials.file-tree-node', $node['children'], 'node')
    @endif
</details>
