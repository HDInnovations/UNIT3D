export default () => ({
    columnHeader: {
        ['x-on:click']() {
            let cellIndex = this.$el.cellIndex + 1;
            let cells = this.$root.querySelectorAll(
                `tbody tr td:nth-child(${cellIndex}) > input[type="checkbox"]`,
            );

            if (Array.from(cells).some((el) => el.checked)) {
                cells.forEach((el) => (el.checked = false));
            } else {
                cells.forEach((el) => (el.checked = true));
            }
        },
        ['x-bind:style']() {
            return {
                cursor: 'pointer',
                userSelect: 'none',
            };
        },
    },
    rowHeader: {
        ['x-on:click']() {
            let rowspan = Number(this.$el.rowSpan);
            let rowIndex = this.$el.parentElement.sectionRowIndex + 1;
            let cells = this.$root.querySelectorAll(
                `tbody tr:nth-child(n + ${rowIndex}):nth-child(-n + ${rowIndex + (rowspan ? rowspan - 1 : 0)}) td > input[type="checkbox"]`,
            );

            if (Array.from(cells).some((el) => el.checked)) {
                cells.forEach((el) => (el.checked = false));
            } else {
                cells.forEach((el) => (el.checked = true));
            }
        },
        ['x-bind:style']() {
            return {
                cursor: 'pointer',
                userSelect: 'none',
            };
        },
    },
    cell: {
        ['x-on:click.self']() {
            let checkbox = this.$el.querySelector('input[type="checkbox"');

            checkbox.checked = !checkbox.checked;
        },
    },
});
