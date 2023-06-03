/* Modal */

export default () => ({
  open: false,

  init() {
    Alpine.bind('dismissModal', () => ({
      '@click.outside'() {
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
