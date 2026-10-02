export default (defaultTab) => ({
    tab: defaultTab,
    tabButton: {
        ['x-on:click']() {
            this.tab = this.$el.dataset.tab;
        },
        ['x-bind:role']() {
            return 'tab';
        },
        ['x-bind:class']() {
            return String(this.tab) === this.$el.dataset.tab
                ? 'panel__tab panel__tab--active'
                : 'panel__tab';
        },
    },
    tabPanel: {
        ['x-show']() {
            return this.tab === this.$el.dataset.tab;
        },
    },
});
