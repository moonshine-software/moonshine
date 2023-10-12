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
      const items = []

      Array.from(this.$el.options ?? []).forEach(function (option) {
        items.push({
          label: option.value,
          value: option.text,
          customProperties: option.dataset?.customProperties
            ? JSON.parse(option.dataset.customProperties)
            : {},
        })
      })

      this.choicesInstance = new Choices(this.$el, {
        allowHTML: true,
        items: items,
        position: 'bottom',
        placeholderValue: this.placeholder,
        searchEnabled: this.searchEnabled,
        removeItemButton: this.removeItemButton,
        shouldSort: this.shouldSort,
        searchResultLimit: 100,
        callbackOnCreateTemplates: function (template) {
          return {
            item: ({classNames}, data) => {
              return template(`
                <div class="${classNames.item} ${
                data.highlighted ? classNames.highlightedState : classNames.itemSelectable
              } ${data.placeholder ? classNames.placeholder : ''}" data-item data-id="${
                data.id
              }" data-value="${data.value}" ${data.active ? 'aria-selected="true"' : ''} ${
                data.disabled ? 'aria-disabled="true"' : ''
              }>
                      <div class="flex gap-x-2 items-center ">
                        ${
                          data.customProperties?.image
                            ? '<div class="zoom-in h-10 w-10 overflow-hidden rounded-md">' +
                              '<img class="h-full w-full object-cover" src="' +
                              data.customProperties.image +
                              '" alt=""></div>'
                            : ''
                        }
                        <span>
                          ${data.label}
                          ${
                            this.config.removeItemButton
                              ? '<button type="button" class="choices__button choices__button--remove" data-button="">Remove item</button>'
                              : ''
                          }
                        </span>
                      </div>

                </div>
              `)
            },
            choice: ({classNames}, data) => {
              return template(`
                <div class="flex gap-x-2 items-center ${classNames.item} ${classNames.itemChoice} ${
                data.disabled ? classNames.itemDisabled : classNames.itemSelectable
              } ${data.value == '' ? 'choices__placeholder' : ''}" data-select-text="${
                this.config.itemSelectText
              }" data-choice ${
                data.disabled
                  ? 'data-choice-disabled aria-disabled="true"'
                  : 'data-choice-selectable'
              } data-id="${data.id}" data-value="${data.value}" ${
                data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
              }>
                      <div class="flex gap-x-2 items-center ">
                          ${
                            data.customProperties?.image
                              ? '<div class="zoom-in h-10 w-10 overflow-hidden rounded-md">' +
                                '<img class="h-full w-full object-cover" src="' +
                                data.customProperties.image +
                                '" alt=""></div>'
                              : ''
                          }
                        <span>
                          ${data.label}
                        </span>
                      </div>
                </div>
            `)
            },
          }
        },
      })

      if (this.$el.dataset.overflow || this.$el.closest('.table-responsive')) {
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
            state.elements.popper.style.width = `${state.elements.reference.offsetWidth}px`
          },
        }

        // Create Popper on showDropdown event
        this.choicesInstance.passedElement.element.addEventListener(
          'showDropdown',
          event => {
            createPopper(
              this.choicesInstance.containerInner.element,
              this.choicesInstance.dropdown.element,
              {
                placement: 'bottom',
                strategy: 'fixed',
                modifiers: [sameWidth],
              }
            )
          },
          false
        )
      }

      if (asyncUrl) {
        this.$el.addEventListener(
          'search',
          debounce(event => {
            let url = new URL(asyncUrl)

            if (event.detail.value.length > 0) {
              if (this.$el.dataset.asyncExtra !== undefined) {
                url.searchParams.append('extra', this.$el.dataset.asyncExtra)
              }

              url.searchParams.append('query', event.detail.value)

              this.fromUrl(url.toString() + (crudFormQuery().length ? '&' + crudFormQuery() : ''))
            }
          }, 300),
          false
        )
      }

      if(this.removeItemButton) {
        this.$el.parentElement.addEventListener('click', event => {
          if (event.target.classList.contains('choices__button--remove')) {
            const choiceElement = event.target.closest('.choices__item')
            const value = choiceElement.getAttribute('data-value')
            this.choicesInstance.removeActiveItemsByValue(value)
            this.choicesInstance._triggerChange(this.choicesInstance._store.placeholderChoice.value)

            if(!this.$el.getAttribute('multiple')) {
              this.choicesInstance._selectPlaceholderChoice(this.choicesInstance._store.placeholderChoice)
            }
          }
        })
      }

    })
  },

  async fromUrl(url) {
    const json = await fetch(url)
      .then(response => {
        return response.json()
      })
      .then(json => {
        return json
      })

    this.choicesInstance.setChoices(json, 'value', 'label', true)
  },
})
