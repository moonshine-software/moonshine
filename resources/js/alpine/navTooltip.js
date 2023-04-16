/* Aside Navigation Tooltip */

import tippy from 'tippy.js'

export default () => ({

  tooltipInstance: null,
  init () {
    this.tooltipInstance = tippy(this.$el, {
      placement: 'right',
      offset: [0, 30],
      content: () => this.$el.querySelector('.menu-inner-text').textContent,
    })
  },
  toggleTooltip () {
    const lgMediaQuery = window.matchMedia(
      '(min-width: 1024px) and (max-width: 1279.98px)')

    if (!this.$data.minimizedMenu && !lgMediaQuery.matches) {
      this.tooltipInstance.hide()
    }
  },

})