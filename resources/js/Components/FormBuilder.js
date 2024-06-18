import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import {addInvalidListener, containsAttribute, isTextInput} from '../Support/Forms.js'
import request from '../Request/Core.js'
import {dispatchEvents as de} from '../Support/DispatchEvents.js'
import {
  getInputs,
  isValidateShow,
  showWhenChange,
  showWhenVisibilityChange,
} from '../Support/ShowWhen.js'

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
            let selector = '.field-' + column + '-wrapper'

            if (typeof html === 'string') {
              const wrapper = t.$root.querySelector(selector)
              const element = wrapper === null ? t.$root.querySelector(selector) : wrapper

              element.outerHTML = html

              addInvalidListener(t.$root.querySelector(selector))

              let input =
                focused &&
                focused !== document.body &&
                isTextInput(focused) &&
                !containsAttribute(focused, 'x-model.lazy')
                  ? t.$root.querySelector(`[x-model='reactive.${column}']`)
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

    if (t.whenFields !== undefined) {
      this.$nextTick(async function () {
        let formId = t.$id('form')
        if (formId === undefined) {
          formId = t.$el.getAttribute('id')
        }

        await t.$nextTick()

        const inputs = t.getInputs(formId)

        const showWhenConditions = {}

        t.whenFields.forEach(field => {
          if (
            inputs[field.changeField] === undefined ||
            inputs[field.changeField].value === undefined
          ) {
            return
          }
          if (showWhenConditions[field.showField] === undefined) {
            showWhenConditions[field.showField] = []
          }
          showWhenConditions[field.showField].push(field)
        })

        for (let key in showWhenConditions) {
          t.showWhenVisibilityChange(showWhenConditions[key], key, inputs, formId)
        }
      })
    }
  },
  precognition() {
    const form = this.$el
    form.querySelector('.precognition_errors').innerHTML = ''
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

        let errors = ''
        let errorsData = data.errors
        for (const error in errorsData) {
          errors = errors + '<div class="mt-2 text-secondary">' + errorsData[error] + '</div>'
        }

        if (data?.message) {
          MoonShine.ui.toast(data.message, 'error')
        }

        form.querySelector('.precognition_errors').innerHTML = errors
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
      ?.querySelector('#async-reset-button')
      ?.removeAttribute('style')
  },

  dispatchEvents(componentEvent, exclude = null, extra = {}) {
    de(componentEvent, '', this, extra)
  },

  asyncFilters(componentEvent, exclude = null) {
    const form = this.$el
    const formData = new FormData(form)

    this.dispatchEvents(componentEvent, exclude, {
      filterQuery: prepareFormQueryString(formData, exclude),
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

  isValidateShow,

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
    form.querySelector('.form_submit_button_loader').style.display = 'none'
    form.querySelector('.form_submit_button').removeAttribute('disabled')
    if (reset) {
      form.reset()
    }
  } else {
    form.querySelector('.form_submit_button').setAttribute('disabled', 'true')
    form.querySelector('.form_submit_button_loader').style.display = 'block'
  }
}
