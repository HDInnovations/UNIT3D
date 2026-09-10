export default (columnCount) => ({
    show: false,
    column: 1,
    columnCount: columnCount,
    showButton: {
        ['x-on:click.prevent']() {
            this.show = true;
            this.$nextTick(() => this.$refs.screenshots.focus());
        },
        ['x-on:keydown.escape.window']() {
            this.show = false;
        },
    },
    screenshots: {
        ['x-ref']: 'screenshots',
        ['x-show']() {
            return this.show;
        },
        ['x-on:click']() {
            this.show = false;
        },
        ['x-on:keydown.down.window']() {
            if (this.show) {
                this.$event.preventDefault();
                this.$event.stopPropagation();
                this.$el.scrollBy(0, this.$el.getElementsByTagName('li')[0].offsetHeight);
            }
        },
        ['x-on:keydown.up.window']() {
            if (this.show) {
                this.$event.preventDefault();
                this.$event.stopPropagation();
                this.$el.scrollBy(0, -1 * this.$el.getElementsByTagName('li')[0].offsetHeight);
            }
        },
        ['x-on:keydown.window']() {
            if (
                isFinite(this.$event.key) &&
                1 <= this.$event.key &&
                this.$event.key <= this.columnCount
            ) {
                this.column = this.$event.key;
            }
        },
        ['x-on:keydown.left.window']() {
            if (this.show) {
                this.$event.preventDefault();
                this.$event.stopPropagation();
                this.column = this.column == 1 ? this.columnCount : this.column - 1;
            }
        },
        ['x-on:keydown.right.window']() {
            if (this.show) {
                this.$event.preventDefault();
                this.$event.stopPropagation();
                this.column = this.column == this.columnCount ? 1 : this.column + 1;
            }
        },
        ['x-on:mousemove.window']() {
            this.column = Math.ceil((this.$event.clientX * this.columnCount) / window.innerWidth);
        },
    },
    image: {
        ['x-bind:class']() {
            return this.column != this.$el.dataset.index && 'comparison__image--hidden';
        },
    },
    container: {
        ['x-bind:class']() {
            return this.column != this.$el.dataset.index && 'comparison__image-container--hidden';
        },
    },
});
