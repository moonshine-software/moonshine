export function getInputs() {
  const inputs = {}
  document.querySelectorAll('#moonshine-form [name]').forEach(element => {
    inputs[inputFieldName(element.getAttribute('name'))] = inputGeValue(element)
  })

  document.querySelectorAll('#moonshine-form [data-input-table]').forEach(element => {
    inputs[inputFieldName(element.getAttribute('data-input-table'))] =
      element.getAttribute('data-input-table')
  })

  return inputs
}

export function showWhenChange(fieldName) {
  fieldName = inputFieldName(fieldName)

  this.whenFields.forEach(field => {
    if (fieldName != field.changeField) {
      return
    }
    this.showWhenVisibilityChange(fieldName, this.getInputs(), field)
  })
}

export function showWhenVisibilityChange(fieldName, inputs, field) {
  if (inputs[field.showField] === undefined) {
    return
  }

  let inputElement = document.querySelector('#moonshine-form [name=' + field.showField + ']')

  if (inputElement === null) {
    inputElement = document.querySelector(
      '#moonshine-form [data-input-table=' + field.showField + ']'
    )
  }

  if (inputElement === null) {
    return
  }

  //TODO in resources/views/components/resource-renderable.blade.php put a field in a container
  let fieldContainer = inputElement.closest('.moonshine-field')
  if(fieldContainer === null) {
    fieldContainer = inputElement.closest('.form-group')
  }
  if(fieldContainer === null) {
    fieldContainer = inputElement
  }

  let validateShow = false

  switch (field.operator) {
    case '=':
      validateShow = inputs[field.changeField] == field.value
      break
    case '!=':
      validateShow = inputs[field.changeField] != field.value
      break
    case '>':
      validateShow = inputs[field.changeField] > field.value
      break
    case '<':
      validateShow = inputs[field.changeField] < field.value
      break
    case '>=':
      validateShow = inputs[field.changeField] >= field.value
      break
    case '<=':
      validateShow = inputs[field.changeField] <= field.value
      break
    case 'in':
      if (Array.isArray(field.value) && Array.isArray(inputs[field.changeField])) {
        for (let i = 0; i < field.value.length; i++) {
          if (inputs[field.changeField].includes(field.value[i])) {
            validateShow = true
            break
          }
        }
      } else {
        validateShow = field.value.includes(inputs[field.changeField])
      }
      break
    case 'not in':
      if (Array.isArray(field.value) && Array.isArray(inputs[field.changeField])) {
        let includes = false
        for (let i = 0; i < field.value.length; i++) {
          if (inputs[field.changeField].includes(field.value[i])) {
            includes = true
            break
          }
        }
        validateShow = !includes
      } else {
        validateShow = !field.value.includes(inputs[field.changeField])
      }
      break
  }

  if (validateShow) {
    fieldContainer.style.removeProperty('display')
  } else {
    fieldContainer.style.display = 'none'
  }
}

export function inputFieldName(inputName) {
  inputName = inputName.replace('[]', '')
  if (inputName.indexOf('slide[') !== -1) {
    inputName = inputName.replace('slide[', '').replace(']', '')
  }
  return inputName
}

export function inputGeValue(element) {
  let value

  const type = element.getAttribute('type')

  if (element.hasAttribute('multiple') && element.options !== undefined) {
    value = []
    for (let option of element.options) {
      if (option.selected) {
        value.push(option.value)
      }
    }
  } else if (type === 'checkbox' || type === 'radio') {
    value = element.checked
  } else {
    value = element.value
  }

  return value
}
