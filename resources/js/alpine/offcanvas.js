/* Offcanvas */

export default () => ({

    open: false,

    init() {
        Alpine.bind('dismissCanvas', () => ({
            '@click.outside'() {
                this.open = false
            },
            '@keydown.escape.window'() {
                this.open = false
            },
        }))
    },

    toggleCanvas() {
        this.open = !this.open
    },

})
