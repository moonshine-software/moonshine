export default () => ({
  open: false,
  popupId: '',
  src: '',
  styles: '',
  auto: true,
  wide: false,

  init() {
    this.popupId = this.$id('img-popup')
  },

  registerInStack() {
    Alpine.store('overlays').register(this.popupId, () => this.close())
  },

  unregisterFromStack() {
    Alpine.store('overlays').unregister(this.popupId)
  },

  show(detail) {
    this.src = detail.src
    this.auto = detail.auto ?? true
    this.wide = detail.wide ?? false
    this.styles = detail.styles ?? ''

    // Move to end of body to ensure it's above all other modals
    const el = document.body.querySelector('[data-img-popup]')
    if (el) {
      document.body.appendChild(el)
    }

    this.open = true
    this.registerInStack()
  },

  close() {
    if (this.open) {
      this.open = false
      this.unregisterFromStack()
    }
  },
})
