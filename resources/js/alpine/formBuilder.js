import {getInputs, showWhenChange, showWhenVisibilityChange} from './showWhenFunctions'

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

        let errors = ''
        let errorsData = errorResponse.response.data.errors
        for (const error in errorsData) {
          errors = errors + '<div class="mt-2 text-secondary">' + errorsData[error] + '</div>'
        }

        form.querySelector('.precognition_errors').innerHTML = errors
      })

    return false
  },

  async(form, events = '', successFunction = '') {
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
        if(successFunction) {
          t.applySuccessFunction(successFunction, response, form, events, t)
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

        if (type !== 'error' && t.inModal) {
          t.toggleModal()
        }

        submitState(form, false, isFormReset)

        if (events !== '' && type !== 'error') {
          events = events.split(',')

          events.forEach(function (event) {
            t.$dispatch(event.replaceAll(/\s/g, ''))
          })
        }
      })
      .catch(errorResponse => {
        if(successFunction) {
          t.applySuccessFunction(successFunction, errorResponse.response, form, events, t)
          return
        }

        if(errorResponse.response.data) {
          const data = errorResponse.response.data

          t.$dispatch('toast', {type: 'error', text: data.message ?? data})
        }

        submitState(form, false)
      })

    return false
  },

  asyncFilters(form, tableName) {
    this.$el
      ?.closest('.offcanvas-template')
      ?.querySelector('#async-reset-button')
      ?.removeAttribute('style')

    const queryString = new URLSearchParams(new FormData(form)).toString()

    this.$dispatch('disable-query-tags')

    this.$dispatch('table-updated-' + tableName, {filters: queryString})

    this.$el?.closest('.offcanvas-template')?.querySelector('.btn-close')?.click()
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

  applySuccessFunction(successFunction, errorResponse, form, events, component)
  {
    const fn = window[successFunction];

    if (typeof fn !== "function") {
      component.$dispatch('toast', {type: 'error', text: 'Error'})
      submitState(form, false)
      throw new Error(successFunction + ' is not a function!');
    }

    fn.apply(null, [errorResponse, form, events, component]);
  }
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
