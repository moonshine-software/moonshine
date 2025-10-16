import tippy, {hideAll} from 'tippy.js'
import typedDataset from '../Support/TypedDataset.js'

export default (config = {}) => ({
  popoverInstance: null,
  config: {
    theme: 'moonshine',
    appendTo: document.body,
    allowHTML: true,
    interactive: true,
    content: reference => {
      const tooltipTitle = reference.getAttribute('title')
      const popoverContent = reference.querySelector('.popover-body-content')
      const clonedContent = popoverContent ? popoverContent.cloneNode(true) : null

      const wrapper = document.createElement('div')
      wrapper.classList.add('popover-body')

      if (tooltipTitle) {
        const titleEl = document.createElement('h5')
        titleEl.classList.add('popover-body-title')
        titleEl.textContent = tooltipTitle
        wrapper.appendChild(titleEl)
      }

      if (clonedContent) {
        clonedContent.classList.remove('hidden')
        wrapper.appendChild(clonedContent)
      }

      return wrapper
    },
    ...config,
  },
  init() {
    this.popoverInstance = tippy(this.$el, {
      ...this.config,
      ...typedDataset(this.$el.dataset),
    })
    this.$el.setAttribute('title', '')
  },
  toggle() {
    if (this.popoverInstance.state.isShown) {
      this.popoverInstance.hide()
    } else {
      this.popoverInstance.show()
    }
  },
  show() {
    this.popoverInstance.show()
  },
  hide() {
    this.popoverInstance.hide()
  },
  hideAll() {
    hideAll()
  },
})
