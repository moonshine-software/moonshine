import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import {
  addInvalidListener,
  containsAttribute,
  isTextInput,
  prepareFormExtraData,
  prepareFormQueryString,
} from '../Support/Forms.js'
import request, {initCallback, prepareUrl} from '../Request/Core.js'
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
      let values = JSON.parse(JSON.stringify(value))

      if (!t.blockWatch) {
        let focused = document.activeElement

        componentRequestData.withAfterResponse(function (data) {
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
                (isTextInput(focused) || focused?.tagName === 'TEXTAREA') &&
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

        const choices = focused.closest('.choices')
        const select = choices?.querySelector('select')

        if (select && select.multiple) {
          await t.$nextTick(() => {
            values[select.getAttribute('data-reactive-column')] =
              select.dataset.choicesValue.split(',')
          })
        }

        request(
          t,
          t.reactiveUrl,
          'post',
          {
            _component_name: t.name,
            values: values,
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
      .post(prepareUrl(form.getAttribute('action')), new FormData(form), {
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

        if (form.dataset?.withoutErrorToast === undefined && data?.message) {
          MoonShine.ui.toast(data.message, 'error')
        }

        form.querySelector('.js-precognition-errors').innerHTML = errors
      })

    return false
  },
  submit() {
    const hasSubmitAttribute = this.$el
      .getAttributeNames()
      .some(attr => attr.startsWith('x-on:submit') || attr.startsWith('@submit'))

    if (!this.$el.checkValidity()) {
      this.$el.reportValidity()

      return
    }

    this.$el.requestSubmit()
  },
  async(events = '', callback = {}) {
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
      action =
        action + (action.includes('?') ? '&' : '?') + new URLSearchParams(formData).toString()
    }

    let componentRequestData = new ComponentRequestData()

    callback = initCallback(callback)

    componentRequestData
      .withSelector(form.dataset.asyncSelector ?? '')
      .withBeforeRequest(callback.beforeRequest)
      .withResponseHandler(callback.responseHandler)
      .withResponseType(form.dataset.asyncResponseType ?? null)
      .withEvents(events)
      .withAfterResponse(function (data, type) {
        if (type !== 'error' && t.inModal && t.autoClose) {
          t.toggleModal()
        }

        if (type !== 'error' && t.inOffCanvas && t.autoClose) {
          t.toggleCanvas()
        }

        if (typeof data !== 'object' || data === null || !('redirect' in data)) {
          submitState(form, false, false)
        }

        return callback.afterResponse
      })
      .withErrorCallback(function (data) {
        submitState(form, false)
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

  dispatchEvents(componentEvent, exclude = null, extra = {}) {
    const form = this.$el.tagName === 'FORM' ? this.$el : this.$el.closest('form')

    extra['_data'] =
      exclude === '*' ? {} : formToJSON(prepareFormExtraData(new FormData(form), exclude))

    de(componentEvent, '', this, extra)
  },

  asyncFilters(componentEvent, exclude = null) {
    const form = this.$el
    const t = this
    const prefix = form.dataset.queryParamPrefix ?? ''
    let formData = new FormData(form)

    const urlSearchParams = new URLSearchParams(window.location.search)

    if (form.dataset.reset) {
      formData = new FormData()
      exclude = '*'
    }

    formData.set(`${prefix}query-tag`, urlSearchParams.get(`${prefix}query-tag`) || '')
    formData.set(`${prefix}sort`, urlSearchParams.get(`${prefix}sort`) || '')

    this._filtersCount()

    this.dispatchEvents(componentEvent, exclude, {
      filterQuery: prepareFormQueryString(formData, exclude),
      component: t,
    })

    form.removeAttribute('data-reset')
  },
  _filtersCount() {
    const form = this.$el
    const formData = new FormData(form)
    const filledFields = new Set()
    const prefix = form.dataset.queryParamPrefix ?? ''

    for (const [name, value] of formData.entries()) {
      if (name.startsWith(prefix + 'filter') && value && value !== '0') {
        const match = name.match(/\[(.*?)]/)
        filledFields.add(match ? match[1] : null)
      }
    }

    document.querySelectorAll('.js-filter-button .badge').forEach(function (element) {
      element.innerHTML = filledFields.size
    })

    const resetBtn = form?.closest('.offcanvas-template')?.querySelector('.js-async-reset-button')
    const resetShow = !form.dataset.reset && filledFields.size

    if (resetShow && resetBtn) {
      resetBtn.removeAttribute('style')
    } else if (resetBtn) {
      resetBtn.style.display = 'none'
    }

    return filledFields.size
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

    this.$el.setAttribute('data-reset', '1')

    this.$el.querySelectorAll('[data-remove-on-form-reset]').forEach(el => el.remove())
  },

  showWhenChange,

  showWhenVisibilityChange,

  getInputs,
})

function submitState(form, loading = true, reset = false) {
  clearErrors(form)

  const button = form.querySelector('[type="submit"]')
  const loader = button.querySelector('.js-form-submit-button-loader')

  if (!button) {
    return
  }

  if (!loader) {
    return
  }

  if (!loading) {
    loader.style.display = 'none'
    button.removeAttribute('disabled')
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

    button.setAttribute('disabled', 'true')
    loader.style.display = 'block'
  }
}

function clearErrors(form) {
  form.querySelectorAll('.form-error').forEach(div => div.remove())
}

function inputsErrors(data, form) {
  if (!data.errors) {
    return
  }

  for (let key in data.errors) {
    let formattedKey = key.replace(/\.(\d+|\w+)/g, '[$1]')
    const inputs = form.querySelectorAll(
      `[name="${formattedKey}"], [data-validation-field="${formattedKey}"]`,
    )
    if (inputs.length) {
      inputs.forEach(input => input.classList.add('form-invalid'))

      const fieldArea = inputs[0].closest('[data-validation-wrapper]') ?? null

      if (fieldArea) {
        const errorDiv = document.createElement('div')
        errorDiv.classList.add('form-error')
        errorDiv.textContent = data.errors[key]
        fieldArea.after(errorDiv)
      }
    }
  }
}
