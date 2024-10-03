import {dispatchEvents as de} from '../Support/DispatchEvents.js'

export default (open = false, asyncUrl = '', autoClose = true) => ({
  open: open,
  id: '',
  asyncUrl: asyncUrl,
  inModal: true,
  asyncLoaded: false,
  autoClose: autoClose,

  init() {
    this.id = this.$id('modal-content')

    if (this.open && this.asyncUrl) {
      this.load(asyncUrl, this.id)
    }

    Alpine.bind('dismissModal', () => ({
      '@keydown.escape.window'() {
        if(this.open) {
          this.open = false

          this.dispatchEvents()
        }
      },
    }))
  },

  dispatchEvents() {
    if(this.open && this.$root?.dataset?.openingEvents) {
      de(this.$root.dataset.openingEvents, '', this)
    }

    if(!this.open && this.$root?.dataset?.closingEvents) {
      de(this.$root.dataset.closingEvents, '', this)
    }
  },

  async toggleModal() {
    this.open = !this.open

    if (this.open && this.asyncUrl && !this.asyncLoaded) {
      await this.load(asyncUrl, this.id)

      this.asyncLoaded = !this.$root.dataset.alwaysLoad
    }

    this.dispatchEvents()
  },
})
