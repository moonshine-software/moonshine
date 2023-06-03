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

  async updateColumn(route, column, value) {
    const response = await axios.put(route, {
      value: value,
      field: column,
    })

    if (response.status === 204) {
      //
    }

    if (response.status === 422) {
      //
    }
  },
})
