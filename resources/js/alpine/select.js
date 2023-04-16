/* Select */

import Choices from 'choices.js'

export default (asyncUrl = '') => ({

  choicesInstance: null,
  placeholder: null,
  searchEnabled: null,
  removeItemButton: null,
  shouldSort: null,

  init() {
    this.placeholder = this.$el.getAttribute('placeholder')
    this.searchEnabled = !!this.$el.dataset.searchEnabled
    this.removeItemButton = !!this.$el.dataset.removeItemButton
    this.shouldSort = !!this.$el.dataset.shouldSort

    this.$nextTick(() => {
      this.choicesInstance = new Choices(this.$el, {
        allowHTML: true,
        position: 'bottom',
        placeholderValue: this.placeholder,
        searchEnabled: this.searchEnabled,
        removeItemButton: this.removeItemButton,
        shouldSort: this.shouldSort,
      })

      if (asyncUrl) {
        this.$el.addEventListener(
          'search',
          (event) => {
            if (event.detail.value.length > 0) {
              this.fromUrl(asyncUrl + '&query=' + event.detail.value)
            }
          },
          false,
        )
      }

    })
  },

  fromUrl(url) {
    this.choicesInstance.setChoices(() => {
        return fetch(url).then((response) => {
          return response.json()
        }).then((json) => {
          return Object.keys(json).map((key) => {
            return {
              value: key,
              label: json[key],
            }
          })
        })
      },
      'value',
      'label',
      true)
  }

})