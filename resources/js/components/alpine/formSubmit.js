export default () => ({
    init() {
        Alpine.bind(this.$root, {
            async 'x-on:submit'(event) {
                event.preventDefault();

                const data = new FormData(this.$root);

                await fetch(this.$root.action, {
                    method: data.get('_method') ?? this.$root.method,
                    body: data,
                    redirect: 'manual',
                });

                if (this.$wire) {
                    await this.$wire.$refresh();
                }

                event.submitter?.popoverTargetElement?.togglePopover();
            },
        });
    },
});
