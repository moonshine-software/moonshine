import {dispatchEvents as de} from '../Support/DispatchEvents.js'
import load from '../Support/AsyncLoadContent.js'

export default (open = false, asyncUrl = '', autoClose = true) => ({
  open: open,
  id: '',
  modalId: '',
  asyncUrl: asyncUrl,
  initialAsyncUrl: asyncUrl,
  inModal: true,
  asyncLoaded: false,
  autoClose: autoClose,

  init() {
    this.id = this.$id('modal-content')
    this.modalId = this.$id('modal')
    this.initialAsyncUrl = this.asyncUrl

    // Register if initially open
    if (this.open) {
      this.registerInStack()

      if (this.asyncUrl) {
        load(this.asyncUrl, this.id)
      }
    }
  },

  registerInStack() {
    Alpine.store('overlays').register(this.modalId, () => this.closeModal())
  },

  unregisterFromStack() {
    Alpine.store('overlays').unregister(this.modalId)
  },

  dispatchEvents() {
    if (this.open && this.$root?.dataset?.openingEvents) {
      de(this.$root.dataset.openingEvents, '', this)
    }

    if (!this.open && this.$root?.dataset?.closingEvents) {
      de(this.$root.dataset.closingEvents, '', this)
    }
  },

  closeModal() {
    if (this.open) {
      this.open = false
      this.unregisterFromStack()
      this.dispatchEvents()
    }
  },

  async toggleModal(event = null) {
    const hasDetail = !!event && 'detail' in event
    const incomingAsyncUrl = hasDetail && typeof event.detail === 'string' ? event.detail : null

    if (hasDetail) {
      const nextAsyncUrl =
        incomingAsyncUrl && incomingAsyncUrl.length > 0 ? incomingAsyncUrl : this.initialAsyncUrl

      if (nextAsyncUrl !== this.asyncUrl) {
        this.asyncUrl = nextAsyncUrl
        this.asyncLoaded = false
      }
    }

    this.open = !this.open

    if (this.open) {
      this.registerInStack()

      if (this.asyncUrl && !this.asyncLoaded) {
        await load(this.asyncUrl, this.id)
        this.asyncLoaded = !this.$root.dataset.alwaysLoad
      }
    } else {
      this.unregisterFromStack()
    }

    this.dispatchEvents()
  },
})
