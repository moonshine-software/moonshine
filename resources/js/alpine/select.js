/* Select */

import Choices from 'choices.js'
import {createPopper} from '@popperjs/core'
import {crudFormQuery} from './formFunctions'
import {debounce} from 'lodash'

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
        searchResultLimit: 100,
      })

      if (!!this.$el.dataset.overflow) {
        // Modifier "Same width" Popper reference
        const sameWidth = {
          name: 'sameWidth',
          enabled: true,
          phase: 'beforeWrite',
          requires: ['computeStyles'],
          fn: ({state}) => {
            state.styles.popper.width = `${state.rects.reference.width}px`
          },
          effect: ({state}) => {
            state.elements.popper.style.width = `${
              state.elements.reference.offsetWidth
            }px`
          }
        }

        // Create Popper on showDropdown event
        this.choicesInstance.passedElement.element.addEventListener('showDropdown', (event) => {
          createPopper(this.choicesInstance.containerInner.element, this.choicesInstance.dropdown.element, {
            placement: 'bottom',
            strategy: 'fixed',
            modifiers: [
              sameWidth,
            ],
          })
        }, false)
      }

      if (asyncUrl) {
        this.$el.addEventListener(
          'search',
          debounce(event => {
            if (event.detail.value.length > 0) {
              let extraQuery = ''

              if (this.$el.dataset.asyncExtra !== undefined) {
                extraQuery = '&extra=' + this.$el.dataset.asyncExtra
              }

              this.fromUrl(
                asyncUrl + '&query=' + event.detail.value + extraQuery + '&' + crudFormQuery()
              )
            }
          }, 300),
          false
        )
      }
    })
  },

  async fromUrl(url) {
    const json = await fetch(url)
      .then(response => {
        return response.json()
      })
      .then(json => {
        return Object.keys(json).map(key => {
          return {
            value: key,
            label: json[key],
          }
        })
      })

    this.choicesInstance.setChoices(json, 'value', 'label', true)
  },
})
