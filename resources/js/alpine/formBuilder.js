import {
  getInputs,
  showWhenChange,
  isValidateShow,
  showWhenVisibilityChange,
} from './showWhenFunctions'
import {moonShineRequest, dispatchEvents as de} from './asyncFunctions'
import {containsAttribute, isTextInput} from './supportFunctions.js'
import {ComponentRequestData} from '../moonshine.js'
import {addInvalidListener} from './formFunctions.js'
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
    t.whenFields = t.initData.whenFields
    t.reactiveUrl = t.initData.reactiveUrl
    let componentRequestData = new ComponentRequestData()

    this.$watch('reactive', async function (value) {
      if (!t.blockWatch) {
        let focused = document.activeElement

        let payload = JSON.parse(
          JSON.stringify(value)
        )

        // choices hack
        let choices = false

        if(focused.tagName.toLowerCase() === 'body') {
          await t.$nextTick

          for (const [key, value] of Object.entries(payload)) {
            let el = t.$root.querySelector(
              `[data-reactive-column='${key}']`,
            )

            if(el.getAttribute('class').includes('choices')) {
              if(el.options.length === 0) {
                payload[key] = []
              }
            }
          }
        }

        if(focused.getAttribute('class').includes('choices')) {
          choices = true

          focused = focused.tagName.toLowerCase()  === 'input'
            ? focused.parentElement.querySelector('select')
            : focused.querySelector('select')
        }

        if(choices && focused.multiple) {
          let values = [];

          for (let i = 0; i < focused.options.length; i++) {
            values.push(focused.options[i].value)
          }

          let c = focused.getAttribute('data-reactive-column')
          payload[c] = values
        }
        // / end of choices hack

        componentRequestData.withAfterCallback(function (data) {
          if(data.fields === undefined) {
            return
          }

          for (let [column, html] of Object.entries(data.fields)) {
            if (typeof html === 'string') {
              const wrapper = t.$root.querySelector('.field-' + column + '-wrapper')
              const element =
                wrapper === null ? t.$root.querySelector('.field-' + column + '-element') : wrapper

              element.outerHTML = html

              addInvalidListener(t.$root.querySelector('.field-' + column + '-element'))

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

        moonShineRequest(
          t,
          t.reactiveUrl,
          'post',
          {
            _component_name: t.name,
            values: payload,
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

        inputsErrors(data, t.$el)

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
      .withAfterErrorCallback(function (data) {
        submitState(form, false)
        inputsErrors(data, t.$el)
      })

    moonShineRequest(
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
    extra['_form'] = formToJSON(new FormData(this.$el))

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

  onChangeField(event) {
    this.showWhenChange(
      event.target.getAttribute('name'),
      event.target.closest('form').getAttribute('id'),
    )
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
  formReset() {
    this.$el.reset()

    Array.from(this.$el.elements).forEach(element => {
      element.dispatchEvent(new Event('reset'))
    })
  },
  filtersCount() {
    const form = this.$el
    const formData = new FormData(form)
    const filledFields = new Set()

    for (const [name, value] of formData.entries()) {
      if (name.startsWith('filters') && value && value !== '0') {
        const match = name.match(/\[(.*?)]/)
        filledFields.add(match ? match[1] : null)
      }
    }

    document.querySelectorAll('.btn-filter .badge').forEach(function (element) {
      element.innerHTML = filledFields.size
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
    const inputs = form.querySelectorAll('[name]')
    if (inputs.length > 0) {
      inputs.forEach(function (element) {
        if (element.classList.contains('form-invalid')) {
          element.classList.remove('form-invalid')
        }
      })
    }

    form.querySelector('.form_submit_button').setAttribute('disabled', 'true')
    form.querySelector('.form_submit_button_loader').style.display = 'block'
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
