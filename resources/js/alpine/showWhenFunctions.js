export function getInputs(formId) {
  const inputs = {}
  document.querySelectorAll('#' + formId + ' [name]').forEach(element => {
    const value = element.getAttribute('name')
    const fieldName = inputFieldName(value)

    inputs[fieldName] = {}

    inputs[fieldName].value = inputGeValue(element)
    inputs[fieldName].type = element.getAttribute('type')
  })

  document.querySelectorAll('#' + formId + ' [data-field-block]').forEach(element => {
    const value = element.getAttribute('data-field-block')
    const fieldName = inputFieldName(value)

    inputs[fieldName] = {}

    inputs[fieldName].value = value
    inputs[fieldName].type = 'text'
  })

  return inputs
}

export function showWhenChange(fieldName, formId) {
  fieldName = inputFieldName(fieldName)

  this.whenFields.forEach(field => {
    if (fieldName !== field.changeField) {
      return
    }

    let showField = field.showField

    let showWhenConditions = []

    this.whenFields.forEach(item => {
      if (showField !== item.showField) {
        return
      }
      showWhenConditions.push(item)
    })

    this.showWhenVisibilityChange(showWhenConditions, showField, this.getInputs(formId), formId)
  })
}

export function showWhenVisibilityChange(showWhenConditions, fieldName, inputs, formId) {
  if (showWhenConditions.length === 0) {
    return
  }

  let inputElement = document.querySelector('#' + formId + ' [name="' + fieldName + '"]')

  if (inputElement === null) {
    inputElement = document.querySelector('#' + formId + ' [data-field-block="' + fieldName + '"]')
  }

  if (inputElement === null) {
    return
  }

  // TODO in resources/views/components/fields-group.blade.php put a field in a container
  let fieldContainer = inputElement.closest('.moonshine-field')
  if (fieldContainer === null) {
    fieldContainer = inputElement.closest('.form-group')
  }
  if (fieldContainer === null) {
    fieldContainer = inputElement
  }

  let countTrueConditions = 0

  showWhenConditions.forEach(field => {
    if (this.isValidateShow(fieldName, inputs, field)) {
      countTrueConditions++
    }
  })

  if (countTrueConditions === showWhenConditions.length) {
    fieldContainer.style.removeProperty('display')
  } else {
    fieldContainer.style.display = 'none'
  }
}

export function isValidateShow(fieldName, inputs, field) {
  let validateShow = false

  let valueInput = inputs[field.changeField].value
  let valueField = field.value

  const inputType = inputs[field.changeField].type

  if (inputType === 'number') {
    valueInput = parseFloat(valueInput)
    valueField = parseFloat(valueField)
  } else if (inputType === 'date' || inputType === 'datetime-local') {
    if (inputType === 'date') {
      valueInput = valueInput + ' 00:00:00'
    }
    valueInput = new Date(valueInput).getTime()

    if (!Array.isArray(valueField)) {
      valueField = new Date(valueField).getTime()
    }
  }

  switch (field.operator) {
    case '=':
      validateShow = valueInput == valueField
      break
    case '!=':
      validateShow = valueInput != valueField
      break
    case '>':
      validateShow = valueInput > valueField
      break
    case '<':
      validateShow = valueInput < valueField
      break
    case '>=':
      validateShow = valueInput >= valueField
      break
    case '<=':
      validateShow = valueInput <= valueField
      break
    case 'in':
      if (Array.isArray(valueInput) && Array.isArray(valueField)) {
        for (let i = 0; i < valueField.length; i++) {
          if (valueInput.includes(valueField[i])) {
            validateShow = true
            break
          }
        }
      } else {
        validateShow = valueField.includes(valueInput)
      }
      break
    case 'not in':
      if (Array.isArray(valueInput) && Array.isArray(valueField)) {
        let includes = false
        for (let i = 0; i < valueField.length; i++) {
          if (valueInput.includes(valueField[i])) {
            includes = true
            break
          }
        }
        validateShow = !includes
      } else {
        validateShow = !valueField.includes(valueInput)
      }
      break
  }

  return validateShow
}

export function inputFieldName(inputName) {
  if (inputName === null) {
    return ''
  }
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
