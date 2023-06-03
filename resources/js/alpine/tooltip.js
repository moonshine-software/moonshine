/* Tooltip */

import tippy from 'tippy.js'

export default (text, config = {}) => ({

    tooltipInstance: null,
    init() {
        this.tooltipInstance = tippy(this.$el, {...config, content: text})
    },

})
