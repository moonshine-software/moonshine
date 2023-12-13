/* Offcanvas */

export default (open = false, asyncUrl = '') => ({
  open: open,
  id: '',
  asyncUrl: asyncUrl,
  asyncLoaded: false,

  init() {
    this.id = this.$id('offcanvas-content')

    if (this.open && this.asyncUrl) {
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

    if (this.open && this.asyncUrl && !this.asyncLoaded) {
      this.load(asyncUrl, this.id)
      this.asyncLoaded = true
    }
  },
})
