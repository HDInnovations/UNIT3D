document.addEventListener('alpine:init', () => {
    Alpine.data('searchPanel', (defaultSortField = null, defaultSortDirection = 'desc') => ({
        sortField: defaultSortField,
        sortDirection: defaultSortDirection,
        page: 1,
        init() {
            this.$root.querySelectorAll('.data-table th:has([data-sort-by])').forEach((th) => {
                Alpine.bind(th, {
                    'x-data'() {
                        return {
                            column: th.dataset.sortBy,
                        };
                    },
                    'x-on:click'() {
                        if (this.sortField === this.column && this.sortDirection === 'asc') {
                            this.sortDirection = 'desc';
                        } else {
                            this.sortDirection = 'asc';
                        }

                        this.sortField = this.column;

                        this.updatePanel();
                    },
                    'x-bind:role'() {
                        return 'columnheader button';
                    },
                });
            });
            Alpine.bind(this.$root.querySelector('.panel__actions'), {
                'x-on:input'(event) {
                    this.updatePanel();
                },
            });
            this.$root
                .querySelectorAll('.pagination__link, .pagination__previous, .pagination__next')
                .forEach((link) => {
                    Alpine.bind(link, {
                        'x-on:click.prevent'() {
                            this.page = new URL(this.$el.href).searchParams.get('page');

                            this.updatePanel();
                        },
                    });
                });
        },
        getFilters() {
            let form = this.$root.querySelector('.panel__actions');
            const formData = new FormData(form);
            const filters = new URLSearchParams(formData);

            form.querySelectorAll('[name]').forEach((el) => {
                let defaultValue = null;

                switch (true) {
                    case el instanceof HTMLInputElement:
                    case el instanceof HTMLTextAreaElement:
                        defaultValue = el.defaultValue;
                        break;
                    case el instanceof HTMLSelectElement:
                        defaultValue = Array.from(el.options).find(
                            (option) => option.defaultSelected,
                        )?.value;
                        break;
                }

                if (filters.get(el.name) === (defaultValue ?? '')) {
                    filters.delete(el.name);
                }
            });

            return filters;
        },
        updatePanel() {
            let params = this.getFilters();

            if (this.sortField !== null && this.sortField !== defaultSortField) {
                params.append('sortField', this.sortField);
            }

            if (this.sortField !== null && this.sortDirection !== defaultSortDirection) {
                params.append('sortDirection', this.sortDirection);
            }

            if (this.page !== 1) {
                params.append('page', this.page);
            }

            const newUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname}?${params}`;
            window.history.pushState({ path: newUrl }, '', newUrl);

            axios
                .get(newUrl, {
                    headers: {
                        'UNIT3D-Request': true,
                    },
                })
                .then((newHtml) => Alpine.morph(this.$root, newHtml.data));
        },
    }));
});
