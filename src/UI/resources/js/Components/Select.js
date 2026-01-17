import Choices from 'choices.js'
import {createPopper} from '@popperjs/core'
import debounce from '../Support/Debounce.js'
import {crudFormQuery, getQueryString, prepareFormExtraData} from '../Support/Forms.js'
import {dispatchEvents as de} from '../Support/DispatchEvents.js'
import {formToJSON} from 'axios'
import {DEFAULT_CONFIG} from '../../../node_modules/choices.js/src/scripts/defaults'
import request from '../Request/Core.js'
import {ComponentRequestData} from '../DTOs/ComponentRequestData'

export default (asyncUrl = '') => ({
  choicesInstance: null,
  placeholder: null,
  searchEnabled: null,
  removeItemButton: null,
  shouldSort: null,
  associatedWith: null,
  searchTerms: null,
  isLoadedOptions: false,
  isMultiple: false,
  morphClearValue: '',
  customOptions: {},
  resolvedOptions: [
    'silent',
    'items',
    'choices',
    'renderChoiceLimit',
    'maxItemCount',
    'addItems',
    'addItemFilter',
    'removeItems',
    'removeItemButton',
    'editItems',
    'allowHTML',
    'duplicateItemsAllowed',
    'delimiter',
    'paste',
    'searchEnabled',
    'searchChoices',
    'searchFields',
    'searchFloor',
    'searchResultLimit',
    'position',
    'resetScrollPosition',
    'addItemFilter',
    'shouldSort',
    'shouldSortItems',
    'sorter',
    'placeholder',
    'placeholderValue',
    'searchPlaceholderValue',
    'prependValue',
    'appendValue',
    'renderSelectedChoices',
    'loadingText',
    'noResultsText',
    'noChoicesText',
    'itemSelectText',
    'uniqueItemText',
    'customAddItemText',
    'addItemText',
    'maxItemText',
    'valueComparer',
    'labelId',
    'classNames',
    'fuseOptions',
    'callbackOnInit',
    'callbackOnCreateTemplates',
  ],

  init() {
    this.placeholder = this.$el.getAttribute('placeholder')
    this.isMultiple = this.$el.getAttribute('multiple')
    this.searchEnabled = !!this.$el.dataset.searchEnabled
    this.removeItemButton = !!this.$el.dataset.removeItemButton
    this.shouldSort = !!this.$el.dataset.shouldSort
    this.associatedWith = this.$el.dataset.associatedWith

    if (this.associatedWith) {
      this.$el.removeAttribute('data-associated-with')
    }

    for (const key in this.$el.dataset) {
      if (this.resolvedOptions.includes(key)) {
        this.customOptions[key] = this.$el.dataset[key]
      }
    }

    this.choicesInstance = new Choices(this.$el, {
      allowHTML: true,
      duplicateItemsAllowed: false,
      position: 'bottom',
      placeholderValue: this.placeholder,
      searchEnabled: this.searchEnabled,
      removeItemButton: this.removeItemButton,
      shouldSort: this.shouldSort,
      loadingText: translates?.loading ?? DEFAULT_CONFIG.loadingText,
      noResultsText: translates?.choices?.no_results ?? DEFAULT_CONFIG.noResultsText,
      noChoicesText: translates?.choices?.no_choices ?? DEFAULT_CONFIG.noChoicesText,
      itemSelectText: translates?.choices?.item_select ?? DEFAULT_CONFIG.itemSelectText,
      uniqueItemText: translates?.choices?.unique_item ?? DEFAULT_CONFIG.uniqueItemText,
      customAddItemText: translates?.choices?.custom_add_item ?? DEFAULT_CONFIG.customAddItemText,
      fuseOptions: {
        threshold: 0,
        ignoreLocation: true,
      },
      addItemText: value => {
        return (
          translates?.choices?.add_item?.replace(':value', `<b>${value}</b>`) ??
          DEFAULT_CONFIG.addItemText(value)
        )
      },
      maxItemText: maxItemCount => {
        return (
          translates?.choices?.max_item?.replace(':count', maxItemCount) ??
          DEFAULT_CONFIG.maxItemText(maxItemCount)
        )
      },
      searchResultLimit: 100,
      callbackOnCreateTemplates: function (strToEl, escapeForTemplate) {
        function normalizeImageData(image) {
          if (typeof image === 'string') {
            return {
              src: image,
              width: 10,
              height: 10,
              objectFit: 'cover',
            }
          }

          return {
            src: image?.src ?? '',
            width: image?.width ?? 10,
            height: image?.height ?? 10,
            objectFit: image?.objectFit ?? 'cover',
          }
        }

        return {
          item: ({classNames}, data, removeItemButton) => {
            const {
              src: imgSrc,
              width,
              height,
              objectFit,
            } = normalizeImageData(data.customProperties?.image)

            return strToEl(`
              <div class="${classNames.item} ${
                data.highlighted ? classNames.highlightedState : classNames.itemSelectable
              } ${data.placeholder ? classNames.placeholder : ''}" data-item data-id="${
                data.id
              }" data-value="${escapeForTemplate(this.config.allowHTML, data.value)}" ${data.active ? 'aria-selected="true"' : ''} ${
                data.disabled ? 'aria-disabled="true"' : ''
              }>
                <div class="flex gap-x-2 items-center">
                  ${
                    imgSrc
                      ? '<div class="zoom-in h-' +
                        height +
                        ' w-' +
                        width +
                        ' overflow-hidden rounded-md">' +
                        '<img class="h-full w-full object-' +
                        objectFit +
                        '" src="' +
                        escapeForTemplate(this.config.allowHTML, imgSrc) +
                        '" alt=""></div>'
                      : ''
                  }
                  <span>
                    ${escapeForTemplate(this.config.allowHTML, data.label)}
                    ${
                      data.value && removeItemButton
                        ? `<button type="button" class="choices__button choices__button--remove" data-button="">${
                            translates?.choices?.remove_item ?? 'x'
                          }</button>`
                        : ''
                    }
                  </span>
                </div>
              </div>
            `)
          },
          choice: ({classNames}, data) => {
            const {
              src: imgSrc,
              width,
              height,
              objectFit,
            } = normalizeImageData(data.customProperties?.image)

            return strToEl(`
              <div class="flex gap-x-2 items-center ${classNames.item} ${classNames.itemChoice} ${
                data.disabled ? classNames.itemDisabled : classNames.itemSelectable
              } ${data.value == '' ? 'choices__placeholder' : ''}" data-select-text="${
                this.config.itemSelectText
              }" data-choice ${
                data.disabled
                  ? 'data-choice-disabled aria-disabled="true"'
                  : 'data-choice-selectable'
              } data-id="${data.id}" data-value="${escapeForTemplate(this.config.allowHTML, data.value)}" ${
                data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
              }>
                <div class="flex gap-x-2 items-center">
                  ${
                    imgSrc
                      ? '<div class="zoom-in h-' +
                        height +
                        ' w-' +
                        width +
                        ' overflow-hidden rounded-md">' +
                        '<img class="h-full w-full object-' +
                        objectFit +
                        '" src="' +
                        escapeForTemplate(this.config.allowHTML, imgSrc) +
                        '" alt=""></div>'
                      : ''
                  }
                  <span>
                    ${escapeForTemplate(this.config.allowHTML, data.label)}
                  </span>
                </div>
              </div>
            `)
          },
        }
      },
      callbackOnInit: async () => {
        this.searchTerms = this.$el.closest('.choices').querySelector('[type="search"]')

        if (asyncUrl && this.$el.dataset.asyncOnInit && !this.$el.dataset.asyncOnInitDropdown) {
          await this.$nextTick
          this.asyncSearch()
        }
      },
      ...this.customOptions,
    })

    this.setDataValues()

    this.$nextTick(() => {
      const el = this.$el

      if (el.value === null || el.value === undefined || el.value === '') {
        return
      }

      let value = this.isMultiple
        ? Array.from(el.selectedOptions).map(option => option.value)
        : el.value

      this.choicesInstance.setChoiceByValue(value)
    })

    this.$el.addEventListener(
      'change',
      () => {
        this.isLoadedOptions = false

        this.$nextTick(() => {
          const value = this.choicesInstance.getValue(true)

          if (this.isMultiple) {
            const selectedValues = Array.isArray(value) ? value.map(String) : []
            for (const option of this.$el.options) {
              option.selected = selectedValues.includes(option.value)
            }
          } else {
            this.$el.value = value ?? ''
          }
        })

        this.setDataValues()
      },
      false,
    )

    if (asyncUrl) {
      this.$el.addEventListener(
        'showDropdown',
        () => {
          if (!this.isLoadedOptions) {
            this.asyncSearch()
          }
        },
        false,
      )
    }

    if (this.associatedWith && asyncUrl) {
      document.querySelector(`[name="${this.associatedWith}"]`).addEventListener(
        'change',
        event => {
          this.choicesInstance.clearStore()
          this.$el.dispatchEvent(new Event('change'))
          this.isLoadedOptions = false
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
      this.searchTerms.addEventListener(
        'input',
        debounce(event => this.asyncSearch(), 300),
        false,
      )
    }

    if (this.removeItemButton) {
      this.$el.parentElement.addEventListener('click', event => {
        if (document.activeElement.type !== 'search') {
          // necessary for reactivity to work
          event.target.closest('.choices')?.querySelector('select')?.focus()
        }

        if (event.target.classList.contains('choices__button--remove')) {
          const choiceElement = event.target.closest('.choices__item')
          const id = choiceElement.getAttribute('data-id')

          if (
            this.choicesInstance._isSelectOneElement &&
            this.choicesInstance._store.placeholderChoice
          ) {
            this.choicesInstance.removeActiveItems(id)
            this.choicesInstance._triggerChange(this.choicesInstance._store.placeholderChoice.value)
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
  },
  setDataValues() {
    if (this.$el.getAttribute('multiple')) {
      this.$el.setAttribute('data-choices-value', this.choicesInstance.getValue(true).join(','))
    }
  },
  morphClear(type) {
    if (type.value && this.morphClearValue !== type.value) {
      this.choicesInstance.clearStore()
      this.morphClearValue = type.value
    }
  },
  async asyncSearch() {
    const query = this.searchTerms.value ?? null
    let canRequest = this.$el.dataset.asyncOnInit || (query !== null && query.length)
    let options = []

    if (canRequest) {
      const url = asyncUrl.startsWith('/')
        ? new URL(asyncUrl, window.location.origin)
        : new URL(asyncUrl)

      url.searchParams.append('query', query)

      const form = this.$el.form
      const inputs = form ? form.querySelectorAll('[name]') : []
      let formQuery = ''

      if (inputs.length) {
        formQuery = crudFormQuery(inputs)
      }

      if (form === null) {
        const value = this.choicesInstance.getValue(true)
        formQuery = getQueryString({value: value === undefined || value === null ? '' : value})
      }

      options = await this.fromUrl(url.toString() + (formQuery.length ? '&' + formQuery : ''))
      options = this.normalizeOptions(options)
    }

    await this.choicesInstance.setChoices(options, 'value', 'label', true)

    this.isLoadedOptions = true
  },
  dispatchEvents(componentEvent, exclude = null, extra = {}) {
    const form = this.$el.closest('form')

    if (exclude !== '*') {
      extra['_data'] = form
        ? formToJSON(prepareFormExtraData(new FormData(form), exclude))
        : {value: this.choicesInstance.getValue(true)}
    }

    de(componentEvent, '', this, extra)
  },
  async fromUrl(url) {
    let options = []

    try {
      let componentRequestData = new ComponentRequestData()
      componentRequestData.withAfterResponse(data => {
        options = data
      })

      await request(this, url, 'get', {}, {}, componentRequestData)
    } catch (e) {}

    return options
  },
  normalizeOptions(items) {
    return items.map(item => {
      if (item.hasOwnProperty('values')) {
        const {values, ...groupData} = item

        const normalizedValues = !Array.isArray(values)
          ? Object.entries(values).map(([value, data]) => ({
              value,
              ...(typeof data === 'object' ? data : {label: data}),
            }))
          : values

        return {
          label: groupData.label,
          id: groupData.id ?? JSON.stringify(groupData.label),
          disabled: groupData.disabled !== undefined ? !!groupData.disabled : false,
          choices: normalizedValues.map(option => this.normalizeOption(option)),
        }
      }

      return this.normalizeOption(item)
    })
  },
  normalizeOption(option) {
    const {properties, ...rest} = option

    return {
      ...rest,
      value: String(rest.value),
      label: rest.label,
      selected: !!rest.selected,
      disabled: !!rest.disabled,
      customProperties: Array.isArray(properties) ? {} : properties || {},
    }
  },
})
