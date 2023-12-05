/* Modal */

export default (open = false) => ({
  open: open,
  id: '',
  inModal: true,

  init() {
    this.id = this.$id('modal-content')
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
