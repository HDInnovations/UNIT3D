document.addEventListener('alpine:init', () => {
    Alpine.data('posterRow', () => ({
        tab: null,
        init() {
            this.tab = this.$root.dataset.defaultTab;
        },
        getPosterRow() {
            let panel = this.$el.closest('.panelV2');
            let posterRows = panel.querySelectorAll('[x-ref="posters"]');

            let posterRow = [...posterRows].find((el) => el.checkVisibility());

            return posterRow;
        },
        scrollLeft: {
            ['x-on:click.prevent']() {
                let posterRow = this.getPosterRow();

                let scrollBy = posterRow.offsetWidth / 2;
                let currentScroll = posterRow.scrollLeft;
                let maxScroll = posterRow.scrollWidth - posterRow.offsetWidth;

                if (currentScroll == 0) {
                    posterRow.scrollTo({
                        left: maxScroll,
                        behavior: 'smooth',
                    });
                } else if (currentScroll < scrollBy) {
                    posterRow.scrollTo({
                        left: 0,
                        behavior: 'smooth',
                    });
                } else {
                    posterRow.scrollBy({
                        left: -1 * scrollBy,
                        behavior: 'smooth',
                    });
                }
            },
        },
        scrollRight: {
            ['x-on:click.prevent']() {
                let posterRow = this.getPosterRow();

                let scrollBy = posterRow.offsetWidth / 2;
                let currentScroll = posterRow.scrollLeft;
                let maxScroll = posterRow.scrollWidth - posterRow.offsetWidth;
                let remainingScroll = maxScroll - currentScroll;

                if (remainingScroll == 0) {
                    posterRow.scrollTo({
                        left: 0,
                        behavior: 'smooth',
                    });
                } else if (remainingScroll < scrollBy) {
                    posterRow.scrollTo({
                        left: maxScroll,
                        behavior: 'smooth',
                    });
                } else {
                    posterRow.scrollBy({
                        left: scrollBy,
                        behavior: 'smooth',
                    });
                }
            },
        },
    }));
});
