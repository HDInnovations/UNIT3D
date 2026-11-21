<details style="margin-left: 20px">
    <summary
        @style([
            'padding: 8px;',
            'list-style-position: outside',
            'cursor: pointer' => $node['type'] === 'directory',
            'list-style-type: none' => $node['type'] === 'file',
        ])
    >
        <span class="file-tree__wrapper">
            @if ($node['type'] === 'file')
                <i class="{{ config('other.font-awesome') }} fa-file file-tree__icon"></i>
                <span class="file-tree__name">{{ $key }}</span>
                <span class="file-tree__size" title="{{ $node['size'] }}&nbsp;B">
                    {{ App\Helpers\StringHelper::formatBytes($node['size'], 2) }}
                </span>
            @else
                <i class="{{ config('other.font-awesome') }} fa-folder file-tree__icon"></i>
                <span class="file-tree__name">{{ $key }}</span>
                <span class="file-tree__count">({{ $node['count'] }})</span>
                <span class="text-info file-tree__size" title="{{ $node['size'] }}&nbsp;B">
                    {{ App\Helpers\StringHelper::formatBytes($node['size'], 2) }}
                </span>
            @endif
        </span>
    </summary>
    @if ($node['type'] === 'directory')
        @each('torrent.partials.file-tree-node', $node['children'], 'node')
    @endif
</details>
