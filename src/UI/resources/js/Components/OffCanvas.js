import {dispatchEvents as de} from '../Support/DispatchEvents.js'

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
        if (this.open) {
          this.open = false

          this.dispatchEvents()
        }
      },
      '@keydown.escape.window'() {
        if (this.open) {
          this.open = false

          this.dispatchEvents()
        }
      },
    }))
  },

  dispatchEvents() {
    if (this.open && this.$root?.dataset?.openingEvents) {
      de(this.$root.dataset.openingEvents, '', this)
    }

    if (!this.open && this.$root?.dataset?.closingEvents) {
      de(this.$root.dataset.closingEvents, '', this)
    }
  },

  async toggleCanvas() {
    this.open = !this.open

    if (this.open && this.asyncUrl && !this.asyncLoaded) {
      await this.load(asyncUrl, this.id)

      this.asyncLoaded = !this.$root.dataset.alwaysLoad
    }

    this.dispatchEvents()
  },
})
