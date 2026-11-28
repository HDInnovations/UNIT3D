export default () => ({
    button: {
        ['x-on:click']() {
            let text = this.$root.querySelector('pre > code').innerHTML.replace(/<br>/g, '\r\n');
            navigator.clipboard.writeText(text);
        },
    },
});
