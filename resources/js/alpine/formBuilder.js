import {getInputs, showWhenChange, showWhenVisibilityChange} from './showWhenFunctions'
import {dispatchEvents, responseCallback} from './asyncFunctions'

export default () => ({
  whenFields: {},
  init(initData) {
    if (initData !== undefined && initData.whenFields !== undefined) {
      this.whenFields = initData.whenFields

      let formId = this.$id('form')
      if (formId === undefined) {
        formId = this.$el.getAttribute('id')
      }

      const inputs = this.getInputs(formId)

      this.whenFields.forEach(field => {
        if (inputs[field.changeField] === undefined) {
          return
        }
        this.showWhenVisibilityChange(field.changeField, inputs, field, formId)
      })
    }
  },
  precognition(form) {
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
          t.$dispatch('toast', {type: 'error', text: data.message})
        }

        form.querySelector('.precognition_errors').innerHTML = errors
      })

    return false
  },

  async(form, events = '', callbackFunction = '') {
    submitState(form, true)
    const t = this
    const method = form.getAttribute('method')
    let action = form.getAttribute('action')
    let formData = new FormData(form)

    if (method === 'get') {
      action = action + '?' + new URLSearchParams(formData).toString()
    }

    axios({
      url: action,
      method: method,
      data: formData,
      headers: {
        Accept: 'application/json',
        ContentType: form.getAttribute('enctype'),
      },
    })
      .then(function (response) {
        if (callbackFunction) {
          responseCallback(callbackFunction, response, form, events, t)

          return
        }

        const data = response.data

        if (data.redirect) {
          window.location = data.redirect
        }

        const type = data.messageType ? data.messageType : 'success'

        if (data.message) {
          t.$dispatch('toast', {
            type: type,
            text: data.message,
          })
        }

        let isFormReset = false

        if (type !== 'error' && t.inModal && t.autoClose) {
          t.toggleModal()
        }

        submitState(form, false, isFormReset)

        dispatchEvents(events, type, t)
      })
      .catch(errorResponse => {
        if (callbackFunction) {
          responseCallback(callbackFunction, errorResponse.response, form, events, t)

          return
        }

        if (errorResponse.response.data) {
          const data = errorResponse.response.data

          t.$dispatch('toast', {type: 'error', text: data.message ?? data})
        }

        submitState(form, false)
      })

    return false
  },

  asyncFilters(form, componentEvent) {
    this.$el
      ?.closest('.offcanvas-template')
      ?.querySelector('#async-reset-button')
      ?.removeAttribute('style')

    const queryString = new URLSearchParams(new FormData(form)).toString()

    this.$dispatch('disable-query-tags')
    this.$dispatch(componentEvent, {filters: queryString})

    this.toggleCanvas()
  },

  onChangeField(event) {
    this.showWhenChange(
      event.target.getAttribute('name'),
      event.target.closest('form').getAttribute('id'),
    )
  },

  formReset() {
    this.$el.reset()
  },

  showWhenChange,

  showWhenVisibilityChange,

  getInputs,
})

function submitState(form, loading = true, isFormReset = false) {
  if (!loading) {
    form.querySelector('.form_submit_button_loader').style.display = 'none'
    form.querySelector('.form_submit_button').removeAttribute('disabled')
    if (isFormReset) {
      form.reset()
    }
  } else {
    form.querySelector('.form_submit_button').setAttribute('disabled', 'true')
    form.querySelector('.form_submit_button_loader').style.display = 'block'
  }
}
