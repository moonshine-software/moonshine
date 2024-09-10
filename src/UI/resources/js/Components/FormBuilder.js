import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import {addInvalidListener, containsAttribute, isTextInput} from '../Support/Forms.js'
import request from '../Request/Core.js'
import {dispatchEvents as de} from '../Support/DispatchEvents.js'
import {getInputs, showWhenChange, showWhenVisibilityChange} from '../Support/ShowWhen.js'
import {formToJSON} from 'axios'

export default (name = '', initData = {}, reactive = {}) => ({
  name: name,
  initData: initData,
  whenFields: {},
  reactiveUrl: '',
  reactive: reactive,
  blockWatch: false,

  init() {
    const t = this
    let componentRequestData = new ComponentRequestData()

    t.whenFields = t.initData.whenFields
    t.reactiveUrl = t.initData.reactiveUrl

    this.$watch('reactive', async function (value) {
      if (!t.blockWatch) {
        let focused = document.activeElement

        componentRequestData.withAfterCallback(function (data) {
          for (let [column, html] of Object.entries(data.fields)) {
            let selectorWrapper = '.field-' + column + '-wrapper'
            let selectorElement = '.field-' + column + '-element'

            if (typeof html === 'string') {
              const wrapper = t.$root.querySelector(selectorWrapper)
              const element = wrapper === null ? t.$root.querySelector(selectorElement) : wrapper

              element.outerHTML = html

              addInvalidListener(element)

              let input =
                focused &&
                focused !== document.body &&
                isTextInput(focused) &&
                !containsAttribute(focused, 'x-model.lazy')
                  ? t.$root.querySelector(
                      `[data-reactive-column='${focused.getAttribute('data-reactive-column')}']`,
                    )
                  : null

              if (input) {
                input.focus()
                delete data.values[input.getAttribute('data-column')]
                const type = input.type
                input.type = 'text'
                input.setSelectionRange(input.value.length, input.value.length)
                input.type = type
              }
            }
          }

          t.blockWatch = true

          for (let [column, value] of Object.entries(data.values)) {
            t.reactive[column] = value
          }

          t.$nextTick(() => (t.blockWatch = false))
        })

        request(
          t,
          t.reactiveUrl,
          'post',
          {
            _component_name: t.name,
            values: value,
          },
          {},
          componentRequestData,
        )
      }
    })

    this.whenFieldsInit()
  },
  whenFieldsInit() {
    const t = this

    if (!t.whenFields.length) {
      return
    }

    this.$nextTick(async function () {
      let formId = t.$id('form')
      if (formId === undefined) {
        formId = t.$el.getAttribute('id')
      }

      await t.$nextTick()

      const inputs = t.getInputs(formId)

      const showWhenFields = {}

      t.whenFields.forEach(field => {
        if (
          inputs[field.changeField] === undefined ||
          inputs[field.changeField].value === undefined
        ) {
          return
        }
        if (showWhenFields[field.showField] === undefined) {
          showWhenFields[field.showField] = []
        }
        showWhenFields[field.showField].push(field)
      })

      for (let key in showWhenFields) {
        t.showWhenVisibilityChange(showWhenFields[key], key, inputs, formId)
      }
    })
  },
  precognition() {
    const form = this.$el
    form.querySelector('.js-precognition-errors').innerHTML = ''
    const t = this

    submitState(form, true)

    axios
      .post(form.getAttribute('action'), new FormData(form), {
        headers: {
          Precognition: true,
          Accept: 'application/json',
          ContentType: form.getAttribute('enctype'),
        },
      })
      .then(function (response) {
        form.submit()
      })
      .catch(errorResponse => {
        submitState(form, false)

        const data = errorResponse.response.data

        inputsErrors(data, t.$el)

        let errors = ''
        let errorsData = data.errors
        for (const error in errorsData) {
          errors = errors + '<div class="mt-2 text-secondary">' + errorsData[error] + '</div>'
        }

        if (data?.message) {
          MoonShine.ui.toast(data.message, 'error')
        }

        form.querySelector('.js-precognition-errors').innerHTML = errors
      })

    return false
  },
  submit() {
    const hasSubmitAttribute = this.$el
      .getAttributeNames()
      .some(attr => attr.startsWith('x-on:submit'))

    if (hasSubmitAttribute) {
      this.$el.dispatchEvent(new Event('submit'))
    } else {
      this.$el.submit()
    }
  },
  async(events = '', callbackFunction = '', beforeFunction = '') {
    const form = this.$el
    submitState(form, true)
    const t = this
    const method = form.getAttribute('method')
    let action = form.getAttribute('action')
    let formData = new FormData(form)

    if (action === '#') {
      action = ''
    }

    if (method?.toLowerCase() === 'get') {
      action = action + '?' + new URLSearchParams(formData).toString()
    }

    let componentRequestData = new ComponentRequestData()

    componentRequestData
      .withBeforeFunction(beforeFunction)
      .withResponseFunction(callbackFunction)
      .withEvents(events)
      .withAfterCallback(function (data, type) {
        if (type !== 'error' && t.inModal && t.autoClose) {
          t.toggleModal()
        }

        submitState(form, false, false)
      })
      .withAfterErrorCallback(function () {
        submitState(form, false)
      })
      .withErrorCallback(function (data) {
        inputsErrors(data, t.$el)
      })

    request(
      t,
      action,
      method,
      formData,
      {
        Accept: 'application/json',
        ContentType: form.getAttribute('enctype'),
      },
      componentRequestData,
    )

    return false
  },

  showResetButton() {
    const form = this.$el

    form
      ?.closest('.offcanvas-template')
      ?.querySelector('.js-async-reset-button')
      ?.removeAttribute('style')
  },

  dispatchEvents(componentEvent, exclude = null, extra = {}) {
    extra['_data'] = formToJSON(new FormData(this.$el))

    de(componentEvent, '', this, extra)
  },

  asyncFilters(componentEvent, exclude = null) {
    const form = this.$el
    const formData = new FormData(form)

    const urlSearchParams = new URLSearchParams(window.location.search)
    formData.set('query-tag', urlSearchParams.get('query-tag') || '')
    formData.set('sort', urlSearchParams.get('sort') || '')

    this.dispatchEvents(componentEvent, exclude, {
      filterQuery: prepareFormQueryString(formData, exclude),
    })

    this.filtersCount()
  },
  filtersCount() {
    const form = this.$el
    const formData = new FormData(form)
    const filledFields = new Set()

    for (const [name, value] of formData.entries()) {
      if (name.startsWith('filter') && value && value !== '0') {
        const match = name.match(/\[(.*?)]/)
        filledFields.add(match ? match[1] : null)
      }
    }

    document.querySelectorAll('.js-filter-button .badge').forEach(function (element) {
      element.innerHTML = filledFields.size
    })
  },
  onChangeField(event) {
    this.showWhenChange(
      event.target.getAttribute('name'),
      event.target.closest('form').getAttribute('id'),
    )
  },

  formReset() {
    this.$el.reset()

    Array.from(this.$el.elements).forEach(element => {
      element.dispatchEvent(new Event('reset'))
    })
  },

  showWhenChange,

  showWhenVisibilityChange,

  getInputs,
})

function prepareFormQueryString(formData, exclude = null) {
  const maxLength = 50
  const filtered = new FormData()

  for (const [key, value] of formData) {
    if (value.length <= maxLength) {
      filtered.append(key, value)
    }
  }

  if (exclude !== null) {
    const excludes = exclude.split(',')

    excludes.forEach(function (excludeName) {
      filtered.delete(excludeName)
    })
  }

  return new URLSearchParams(filtered).toString()
}

function submitState(form, loading = true, reset = false) {
  if (!loading) {
    form.querySelector('.js-form-submit-button-loader').style.display = 'none'
    form.querySelector('.js-form-submit-button').removeAttribute('disabled')
    if (reset) {
      form.reset()
    }
  } else {
    const inputs = form.querySelectorAll('[name]')
    if (inputs.length > 0) {
      inputs.forEach(function (element) {
        if (element.classList.contains('form-invalid')) {
          element.classList.remove('form-invalid')
        }
      })
    }

    form.querySelector('.js-form-submit-button').setAttribute('disabled', 'true')
    form.querySelector('.js-form-submit-button-loader').style.display = 'block'
  }
}

function inputsErrors(data, form) {
  if (!data.errors) {
    return
  }
  for (let key in data.errors) {
    let formattedKey = key.replace(/\.(\d+|\w+)/g, '[$1]')
    const input = form.querySelector(`[name="${formattedKey}"]`)
    if (input) {
      input.classList.add('form-invalid')
    }
  }
}
