export default () => ({
    complete: {
        ['x-on:click.ctrl.prevent']() {
            this.toggle(document, ['complete', 'specials', 'special', 'season', 'pack', 'episode']);
        },
    },
    specials: {
        ['x-on:click.ctrl.prevent']() {
            this.toggle(document, ['complete', 'specials', 'special', 'season', 'pack', 'episode']);
        },
    },
    special: {
        ['x-on:click.ctrl.prevent']() {
            this.toggle(this.$el.parentNode.parentNode, ['special']);
        },
    },
    season: {
        ['x-on:click.ctrl.prevent']() {
            this.toggle(document, ['complete', 'specials', 'special', 'season', 'pack', 'episode']);
        },
    },
    pack: {},
    episode: {
        ['x-on:click.ctrl.prevent']() {
            this.toggle(this.$el.parentNode.parentNode, ['episode']);
        },
    },
    all: {
        ['x-on:click.prevent']() {
            this.toggle(document, ['complete', 'specials', 'special', 'season', 'pack', 'episode']);
        },
    },
    toggle(root, dropdowns) {
        let query = dropdowns.map((dropdown) => `details:has([x-bind="${dropdown}"])`).join();

        console.log(query);

        let elements = root.querySelectorAll(query);

        if (Array.from(elements).every((el) => el.open)) {
            elements.forEach((el) => (el.open = false));
        } else {
            elements.forEach((el) => (el.open = true));
        }
    },
});
