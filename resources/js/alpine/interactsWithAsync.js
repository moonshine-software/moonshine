import {moonShineRequest} from './asyncFunctions.js'
import {ComponentRequestData} from '../moonshine.js'

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

  async requestWithFieldValue(route, column, value = null) {
    if (value === null) {
      value = this.$el.value
    }

    if (value === null && (this.$el.type === 'checkbox' || this.$el.type === 'radio')) {
      value = this.$el.checked
    }

    if (this.$el.tagName.toLowerCase() === 'select' && this.$el.multiple) {
      value = []
      for (let i = 0; i < this.$el.options.length; i++) {
        let option = this.$el.options[i]
        if (option.selected) {
          value.push(option.value)
        }
      }
    }

    const componentRequestData = new ComponentRequestData()
    componentRequestData.fromDataset(this.$el?.dataset ?? {})

    moonShineRequest(
      this,
      route,
      this.$el?.dataset?.asyncMethod ?? 'put',
      {
        value: value,
        field: column,
      },
      {},
      componentRequestData,
    )
  },
})
