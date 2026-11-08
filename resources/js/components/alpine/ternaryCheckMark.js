export default (property) => ({
    state: property,
    updateTernaryCheckboxProperties(el, state) {
        el.indeterminate = this.state === 'exclude';
        el.checked = this.state === 'include';
    },
    getNextTernaryCheckboxState(state) {
        return this.state === 'include' ? 'exclude' : this.state === 'exclude' ? 'any' : 'include';
    },
    input: {
        ['x-init']() {
            this.updateTernaryCheckboxProperties(this.$el, this.state);
        },
        ['x-on:click']() {
            this.state = this.getNextTernaryCheckboxState(this.state);
            this.updateTernaryCheckboxProperties(this.$el, this.state);
        },
        ['x-bind:checked']() {
            return this.state === 'include';
        },
    },
});
