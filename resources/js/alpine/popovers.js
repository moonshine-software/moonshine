/* Popovers */

import tippy from 'tippy.js'

export default (config = {}) => ({
  popoverInstance: null,
  tippyConfig: {
    theme: 'ms-light',
    appendTo: document.body,
    allowHTML: true,
    interactive: true,
    content: reference => {
      const tooltipTitle = reference.getAttribute('title')
      return `<div class="popover-body">${
        tooltipTitle ? `<h5 class="title">${tooltipTitle}</h5>` : ''
      } ${reference.getAttribute('data-content')}</div>`
    },
    ...config,
  },
  init() {
    this.popoverInstance = tippy(this.$el, this.tippyConfig)
  },
})
