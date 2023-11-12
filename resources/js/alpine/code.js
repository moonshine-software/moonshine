/* Flask */

import CodeFlask from 'codeflask'

export default (config = {}) => ({
  flaskInstance: null,
  init() {
    const container = this.$el.closest('.code-container')
    const input = container.querySelector('.code-source')
    this.flaskInstance = new CodeFlask(this.$el, config)
    this.flaskInstance.onUpdate(code => (input.value = code))
    this.flaskInstance.updateCode(input.value)
  },
})
