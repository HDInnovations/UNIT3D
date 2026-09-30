// Torrent history: optional column selection (remembered per browser) and
// the moderator immunity confirmation.
const COLUMN_STORAGE_KEY = 'vltava.history.columns';

function readStoredColumns(available) {
    try {
        const stored = JSON.parse(window.localStorage.getItem(COLUMN_STORAGE_KEY) ?? '[]');

        return Array.isArray(stored) ? stored.filter((column) => available.includes(column)) : [];
    } catch {
        return [];
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('userTorrentsColumns', (available) => ({
        advanced: false,
        columns: [],
        init() {
            this.columns = readStoredColumns(available);

            this.$watch('columns', (columns) => {
                try {
                    window.localStorage.setItem(COLUMN_STORAGE_KEY, JSON.stringify(columns));
                } catch {
                    // Storage is unavailable (private mode); selection stays for this page only.
                }
            });
        },
    }));

    Alpine.data('userHistory', (confirmationTitle) => ({
        updateImmune(immune) {
            const torrentId = this.$el.closest('[data-history-id]').dataset.historyId;

            Swal.fire({
                title: confirmationTitle,
                text: atob(this.$el.dataset.b64DeletionMessage),
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$wire.updateImmune(torrentId, immune);
                }
            });
        },
    }));
});
