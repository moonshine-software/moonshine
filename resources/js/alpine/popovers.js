/* Popovers */

import tippy, {hideAll} from 'tippy.js'

export default (config = {}) => ({
  popoverInstance: null,
  config: {
    theme: 'ms-light',
    appendTo: document.body,
    allowHTML: true,
    interactive: true,
    content: reference => {
      const tooltipTitle = reference.getAttribute('title')
      return `<div class="popover-body">${
        tooltipTitle ? `<h5 class="title">${tooltipTitle}</h5>` : ''
      } ${reference.querySelector('.popover-content').innerHTML}</div>`
    },
    ...config,
  },
  init() {
    this.popoverInstance = tippy(this.$el, {
      ...this.config,
      ...this.$el.dataset ?? {}
    })
  },
  toggle() {
    if(this.popoverInstance.state.isShown) {
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
  }
})
