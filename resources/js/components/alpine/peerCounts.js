// Keeps a torrent's seeder/leecher/completed counts live by polling the JSON
// counts endpoint. Polling pauses while the tab is hidden.
document.addEventListener('alpine:init', () => {
    Alpine.data('peerCounts', (url, initial, intervalMs = 10000) => ({
        counts: initial,
        timer: null,

        init() {
            this.timer = setInterval(() => this.refresh(), intervalMs);
        },

        destroy() {
            clearInterval(this.timer);
        },

        async refresh() {
            if (document.hidden) {
                return;
            }

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });

                if (response.ok) {
                    this.counts = await response.json();
                }
            } catch {
                // Keep the last known counts on transient network errors.
            }
        },
    }));
});
