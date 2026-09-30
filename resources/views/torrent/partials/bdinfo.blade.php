<section class="panelV2 torrent-bdinfo" x-data="bdinfo">
    <header class="panel__header">
        <h2 class="panel__heading">
            <i class="{{ config('other.font-awesome') }} fa-compact-disc"></i>
            {{ __('media-interface.torrent.bdinfo-heading') }}
        </h2>
        <div class="panel__actions">
            <div class="panel__action">
                <button class="form__button form__button--text" x-data x-on:click.stop="copy">
                    {{ __('media-interface.torrent.clipboard-copy-action') }}
                </button>
            </div>
        </div>
    </header>
    <div class="panel__body">
        <div class="bbcode-rendered">
            <pre><code x-ref="bdinfo">{{ $torrent->bdinfo }}</code></pre>
        </div>
    </div>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('bdinfo', () => ({
                copy() {
                    navigator.clipboard.writeText(this.$refs.bdinfo.textContent);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        title: @js(__('media-interface.torrent.clipboard-copied-message')),
                    });
                },
            }));
        });
    </script>
</section>
