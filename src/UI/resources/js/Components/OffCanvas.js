import {dispatchEvents as de} from '../Support/DispatchEvents.js'
import load from '../Support/AsyncLoadContent.js'

export default (open = false, asyncUrl = '', autoClose = true) => ({
  open: open,
  id: '',
  asyncUrl: asyncUrl,
  inOffCanvas: true,
  asyncLoaded: false,
  autoClose: autoClose,

  init() {
    this.id = this.$id('offcanvas-content')

    if (this.open && this.asyncUrl) {
      load(asyncUrl, this.id)
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
      await load(asyncUrl, this.id)

      this.asyncLoaded = !this.$root.dataset.alwaysLoad
    }

    this.dispatchEvents()
  },
})
