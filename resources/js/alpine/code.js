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

    this.$el
      .querySelector('textarea')
      .addEventListener('focus', () => container.classList.add('is-focused'))

    this.$el
      .querySelector('textarea')
      .addEventListener('blur', () => container.classList.remove('is-focused'))

    input.addEventListener('reset', () => this.init())
  },
})
