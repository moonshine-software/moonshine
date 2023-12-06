/* Offcanvas */

export default (open = false, asyncUrl = '') => ({
  open: open,
  id: '',
  asyncUrl: asyncUrl,

  init() {
    this.id = this.$id('offcanvas-content')

    if(this.asyncUrl) {
      this.load(asyncUrl, this.id)
    }

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
