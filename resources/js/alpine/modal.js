/* Modal */

export default (open = false, asyncUrl = '') => ({
  open: open,
  id: '',
  asyncUrl: asyncUrl,
  inModal: true,

  init() {
    this.id = this.$id('modal-content')

    if (this.asyncUrl) {
      this.load(asyncUrl, this.id)
    }

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
