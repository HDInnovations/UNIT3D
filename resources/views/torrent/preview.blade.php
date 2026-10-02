<section class="panelV2 upload-preview" aria-label="{{ __('upload-flow.preview.aria-label') }}">
    <div class="panel__body upload-preview__body">
        @if ($coverUrl)
            <img class="upload-preview__cover" src="{{ $coverUrl }}" alt="" />
        @endif
        <div class="upload-preview__main">
            <h3 class="upload-preview__title">{{ $title }}</h3>

            @if ($folderName && $folderName !== $title)
                <p class="form__hint upload-preview__folder">
                    {{ __('upload-flow.preview.folder-name') }}: {{ $folderName }}
                </p>
            @endif

            <dl class="key-value upload-preview__facts">
                <div class="key-value__group">
                    <dt>{{ __('torrent.category') }}</dt>
                    <dd>{{ $category->name }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('torrent.type') }}</dt>
                    <dd>{{ $type->name }}</dd>
                </div>
                @if ($resolution)
                    <div class="key-value__group">
                        <dt>{{ __('torrent.resolution') }}</dt>
                        <dd>{{ $resolution->name }}</dd>
                    </div>
                @endif
                @if ($scope)
                    <div class="key-value__group">
                        <dt>{{ __('upload-flow.preview.scope-label') }}</dt>
                        <dd>{{ $scope }}</dd>
                    </div>
                @endif
                <div class="key-value__group">
                    <dt>{{ __('vltava.media.size') }}</dt>
                    <dd title="{{ $size }}&#x202F;B">{{ \App\Helpers\StringHelper::formatBytes($size) }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('upload-flow.preview.file-count-label') }}</dt>
                    <dd>{{ $count }}</dd>
                </div>
                @if ($region)
                    <div class="key-value__group">
                        <dt>{{ __('media-interface.torrent.region-full-disc-hint') }}</dt>
                        <dd>{{ $region->name }}</dd>
                    </div>
                @endif
                @if ($distributor)
                    <div class="key-value__group">
                        <dt>{{ __('media-interface.torrent.distributor-full-disc-hint') }}</dt>
                        <dd>{{ $distributor->name }}</dd>
                    </div>
                @endif
                <div class="key-value__group">
                    <dt>{{ __('upload-flow.preview.visibility-label') }}</dt>
                    <dd>
                        {{ $anon ? __('upload-flow.preview.visibility-anon') : __('upload-flow.preview.visibility-named') }}
                        @if ($personal)
                            &middot; {{ __('torrent.personal-release') }}
                        @endif
                        @if ($modQueueOptIn)
                            &middot; {{ __('upload-flow.preview.visibility-mod-queue') }}
                        @endif
                    </dd>
                </div>
            </dl>

            @if (!empty($keywords))
                <p class="upload-preview__keywords">
                    <strong>{{ __('torrent.keywords') }}:</strong> {{ implode(', ', $keywords) }}
                </p>
            @endif
        </div>
    </div>
    <div class="panel__body upload-preview__description bbcode-rendered">
        @bbcode($description)
    </div>
</section>
