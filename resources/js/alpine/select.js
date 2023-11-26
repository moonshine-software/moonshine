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
  associatedWith: null,
  observer: null,
  options: [],

  init() {
    this.placeholder = this.$el.getAttribute('placeholder')
    this.searchEnabled = !!this.$el.dataset.searchEnabled
    this.removeItemButton = !!this.$el.dataset.removeItemButton
    this.shouldSort = !!this.$el.dataset.shouldSort
    this.associatedWith = this.$el.dataset.associatedWith

    if (this.associatedWith) {
      this.$el.removeAttribute('data-associated-with')
    }

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
        ...this.$el.dataset,
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
        callbackOnInit: () => {
          if (asyncUrl) {
            this.asyncSearch()

            const options = {
              root: this.$el.closest('.choices').querySelector('.choices__list .choices__list'),
              rootMargin: '0px',
              threshold: 1.0,
            };

            const callback = (entries, observer) => {
              if (entries[0].isIntersecting) {
                this.asyncSearch(null, false)
              }
            }

            this.observer = new IntersectionObserver(callback, options)
          }
        },
      })

      if (this.associatedWith && asyncUrl) {
        document.querySelector(`[name="${this.associatedWith}"]`).addEventListener(
          'change',
          event => {
            this.choicesInstance.clearStore()
            this.asyncSearch()
          },
          false,
        )
      }

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
              },
            )
          },
          false,
        )
      }

      if (asyncUrl) {
        const search_terms = this.$el.closest('.choices').querySelector('[name="search_terms"]')

        search_terms.addEventListener(
          'input',
          debounce(event => this.asyncSearch(event.target.value), 300),
          false,
        )
        this.$el.addEventListener(
          'change',
          () => search_terms.dispatchEvent(new Event('input')),
          false,
        )
      }

      if (this.removeItemButton) {
        this.$el.parentElement.addEventListener('click', event => {
          if (event.target.classList.contains('choices__button--remove')) {
            const choiceElement = event.target.closest('.choices__item')
            const id = choiceElement.getAttribute('data-id')

            if (
              this.choicesInstance._isSelectOneElement &&
              this.choicesInstance._store.placeholderChoice
            ) {
              this.choicesInstance.removeActiveItems(id)

              this.choicesInstance._triggerChange(
                this.choicesInstance._store.placeholderChoice.value,
              )

              this.choicesInstance._selectPlaceholderChoice(
                this.choicesInstance._store.placeholderChoice,
              )
            } else {
              const {items} = this.choicesInstance._store

              const itemToRemove = id && items.find(item => item.id === parseInt(id, 10))

              if (!itemToRemove) {
                return
              }

              this.choicesInstance._removeItem(itemToRemove)
              this.choicesInstance._triggerChange(itemToRemove.value)
            }
          }
        })
      }
    })
  },

  async asyncSearch(query = null, preloader = true) {
    const url = new URL(asyncUrl)

    if (preloader) {
      this.options = []
    }

    if (query !== null && query.length) {
      url.searchParams.append('query', query)
    }

    url.searchParams.append('offset', this.options.length)

    const form = this.$el.form
    const formQuery = crudFormQuery(form.querySelectorAll('[name]'))

    const options = await this.fromUrl(url.toString() + (formQuery.length ? '&' + formQuery : ''))

    this.options = [...this.options, ...options]

    this.choicesInstance.setChoices(this.options, 'value', 'label', true)

    const target = this.$el
      .closest('.choices')
      .querySelector('.choices__list .choices__list .choices__item:last-child');

    this.observer.observe(target);
  },

  fromUrl(url) {
    return fetch(url)
      .then(response => {
        return response.json()
      })
      .then(json => {
        return json
      })
  },
})
