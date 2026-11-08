export default () => ({
    toggleState: false,
    isToggledOn() {
        return this.toggleState === true;
    },
    isToggledOff() {
        return this.toggleState === false;
    },
    toggle() {
        this.toggleState = !this.toggleState;
    },
    toggleOn() {
        this.toggleState = true;
    },
    toggleOff() {
        this.toggleState = false;
    },
});
