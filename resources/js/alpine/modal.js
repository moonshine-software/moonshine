/* Modal */

export default () => ({
  open: false,

  init() {
    Alpine.bind('dismissModal', () => ({
      '@click.stop'() {
        this.open = false
      },
      '@keydown.escape.window'() {
        this.open = false
      },
    }))
  },

  toggleModal() {
    this.open = !this.open
  },
})
