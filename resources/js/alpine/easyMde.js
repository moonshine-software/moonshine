/* EasyMDE */

export default (options = {}) => ({
  easyMDEInstance: null,
  options: options,

  init() {
    this.easyMDEInstance = new EasyMDE(this.config())

    this.$el.addEventListener('reset', () => {
      this.easyMDEInstance.value(this.$el.value)
    })
  },

  config() {
    for (const key in this.callbacks) {
      this.callbacks[key] = new Function('return ' + this.callbacks[key])()
    }

    return Object.assign(
      {
        element: this.$el,
        renderingConfig: {
          sanitizerFunction: renderedHTML => {
            return DOMPurify.sanitize(renderedHTML, {
              USE_PROFILES: {
                html: true,
              },
            })
          },
        },
      },
      this.options
    )
  }
})
