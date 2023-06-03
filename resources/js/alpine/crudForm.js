import {
  getInputs,
  inputFieldName,
  showWhenChange,
  showWhenVisibilityChange,
} from './showWhenFunctions'

export default () => ({
  whenFields: {},
  init(initData) {
    if (initData !== undefined && initData.whenFields !== undefined) {
      this.whenFields = initData.whenFields

      const inputs = this.getInputs()

      this.whenFields.forEach(field => {
        if (inputs[field.changeField] === undefined) {
          return
        }
        this.showWhenVisibilityChange(field.changeField, inputs, field)
      })
    }
  },
  precognition(form) {
    form.querySelector('.form_submit_button').setAttribute('disabled', 'true')
    form.querySelector('.form_submit_button_loader').style.display = 'block'
    form.querySelector('.precognition_errors').innerHTML = ''

    axios
      .post(form.getAttribute('action'), new FormData(form), {
        headers: {
          Precognition: true,
          Accept: 'application/json',
          'Content-Type': form.getAttribute('enctype'),
        },
      })
      .then(function (response) {
        form.submit()
      })
      .catch(errorResponse => {
        form.querySelector('.form_submit_button_loader').style.display = 'none'
        form.querySelector('.form_submit_button').removeAttribute('disabled')

        let errors = ''
        let errorsData = errorResponse.response.data.errors
        for (const error in errorsData) {
          errors = errors + '<div class="mt-2 text-pink">' + errorsData[error] + '</div>'
        }

        form.querySelector('.precognition_errors').innerHTML = errors
      })

    return false
  },

  onChangeField(event) {
    this.showWhenChange(event.target.getAttribute('name'))
  },

  showWhenChange,

  showWhenVisibilityChange,

  getInputs,
})
