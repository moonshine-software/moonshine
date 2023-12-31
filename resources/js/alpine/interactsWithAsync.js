import {moonShineRequest} from './asyncFunctions.js'

export default () => ({
  async load(url, id) {
    const {data, status} = await axios.get(url)

    if (status === 200) {
      let containerElement = document.getElementById(id)

      containerElement.innerHTML = data

      const scriptElements = containerElement.querySelectorAll('script')

      Array.from(scriptElements).forEach(scriptElement => {
        const clonedElement = document.createElement('script')

        Array.from(scriptElement.attributes).forEach(attribute => {
          clonedElement.setAttribute(attribute.name, attribute.value)
        })

        clonedElement.text = scriptElement.text

        scriptElement.parentNode.replaceChild(clonedElement, scriptElement)
      })
    }
  },

  async requestWithFieldValue(
    route,
    column,
    value = null,
  ) {
    if (value === null) {
      value = this.$el.value
    }

    if (value === null && (this.$el.type === 'checkbox' || this.$el.type === 'radio')) {
      value = this.$el.checked
    }

    const t = this

    t.selector = this.$el?.dataset?.asyncSelector
    t.method = this.$el?.dataset?.asyncMethod
    t.events = this.$el?.dataset?.asyncEvents
    t.callback = this.$el?.dataset?.asyncCallback

    moonShineRequest(
      t,
      route,
      'put',
      {
        value: value,
        field: column,
      }
    )
  },
})
