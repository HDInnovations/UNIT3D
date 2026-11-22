<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">
            <i class="{{ config('other.font-awesome') }} fa-sticky-note"></i>
            {{ __('common.description') }}
        </h2>
        <div class="panel__actions">
            <div class="panel__action">
                <button
                    class="form__button form__button--text"
                    x-data="description"
                    x-on:click.stop="copy"
                >
                    Copy
                </button>
            </div>
        </div>
    </header>
    <div class="panel__body bbcode-rendered">
        @bbcode($torrent->description)
    </div>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('description', () => ({
                copy() {
                    text = document.createElement('textarea');
                    text.innerHTML = decodeURIComponent(
                        escape(atob('{{ base64_encode($torrent->description) }}')),
                    );
                    navigator.clipboard.writeText(text.value);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        title: 'Copied to clipboard!',
                    });
                },
            }));
        });
    </script>
</section>
