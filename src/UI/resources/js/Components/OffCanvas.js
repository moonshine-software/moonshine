import {dispatchEvents as de} from '../Support/DispatchEvents.js'
import load from '../Support/AsyncLoadContent.js'

export default (open = false, asyncUrl = '', autoClose = true) => ({
  open: open,
  id: '',
  canvasId: '',
  asyncUrl: asyncUrl,
  inOffCanvas: true,
  asyncLoaded: false,
  autoClose: autoClose,

  init() {
    this.id = this.$id('offcanvas-content')
    this.canvasId = this.$id('offcanvas')

    // Register if initially open
    if (this.open) {
      this.registerInStack()

      if (this.asyncUrl) {
        load(asyncUrl, this.id)
      }
    }
  },

  registerInStack() {
    Alpine.store('overlays').register(this.canvasId, () => this.closeCanvas())
  },

  unregisterFromStack() {
    Alpine.store('overlays').unregister(this.canvasId)
  },

  dispatchEvents() {
    if (this.open && this.$root?.dataset?.openingEvents) {
      de(this.$root.dataset.openingEvents, '', this)
    }

    if (!this.open && this.$root?.dataset?.closingEvents) {
      de(this.$root.dataset.closingEvents, '', this)
    }
  },

  closeCanvas() {
    if (this.open) {
      this.open = false
      this.unregisterFromStack()
      this.dispatchEvents()
    }
  },

  async toggleCanvas() {
    this.open = !this.open

    if (this.open) {
      this.registerInStack()

      if (this.asyncUrl && !this.asyncLoaded) {
        await load(asyncUrl, this.id)
        this.asyncLoaded = !this.$root.dataset.alwaysLoad
      }
    } else {
      this.unregisterFromStack()
    }

    this.dispatchEvents()
  },
})
