/* Modal */

export default (open = false) => ({
  open: open,

  init() {
    Alpine.bind('dismissModal', () => ({
      '@keydown.escape.window'() {
        this.open = false
      },
    }))
  },

  toggleModal() {
    this.open = !this.open
  },
})
