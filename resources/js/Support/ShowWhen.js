export function getInputs(formId) {
  const inputs = {}

  const form = document.getElementById(formId);

  form.querySelectorAll('[name]').forEach(element => {
    const value = element.getAttribute('name')
    const fieldName = inputFieldName(value)

    inputs[fieldName] = {
      value: inputGetValue(element),
      type: element.getAttribute('type')
    }
  })

  form.querySelectorAll('[data-show-when-field]').forEach(element => {
    const value = element.getAttribute('data-show-when-field')
    const fieldName = inputFieldName(value)

    inputs[fieldName] = {
      value,
      type: 'text'
    }
  })

  form.querySelectorAll('[data-show-when-column]').forEach(element => {
    const fieldName = element.getAttribute('data-show-when-column')

    inputs[fieldName] = {
      value: inputGetValue(element),
      type: element.getAttribute('type')
    }
  })

  return inputs
}

export function showWhenChange(fieldName, formId) {
  fieldName = inputFieldName(fieldName)

  const showWhenFields = []

  this.whenFields.forEach(field => {
    if (fieldName !== field.changeField) {
      return
    }

    let showField = field.showField

    if(! showWhenFields[showField]) {
      showWhenFields[showField] = []
    }

    showWhenFields[showField].push(field)
  })

  for (let showField in showWhenFields) {
    this.showWhenVisibilityChange(showWhenFields[showField], showField, this.getInputs(formId), formId)
  }
}

export function showWhenVisibilityChange(showWhenFields, fieldName, inputs, formId) {
  if (showWhenFields.length === 0) {
    return
  }

  let inputElement = document.querySelector('#' + formId + ' [name="' + fieldName + '"]')

  if (inputElement === null) {
    inputElement = document.querySelector('#' + formId + ' [data-show-when-field="' + fieldName + '"]')
  }

  if (inputElement === null) {
    inputElement = document.querySelector('#' + formId + ' [data-show-when-column="' + fieldName + '"]')
  }

  if (inputElement === null) {
    return
  }

  let countTrueConditions = 0;
  showWhenFields.forEach(field => {
    if (isShowField(fieldName, inputs, field)) {
      countTrueConditions++
    }
  })

  const showWhenSubmit = document.querySelector(`#${formId}`).getAttribute('data-submit-show-when')

  if(inputElement.closest('table[data-inside=field]')) {
    // If input is in a table, then find all tables with this input
    const tablesWithInput = []

    // Only data-show-when-field is used in tables, see in UI/Collections/Fields.php(prepareReindex)
    document.querySelectorAll('[data-show-when-field="' + fieldName + '"]').forEach(function (element) {
      let inputTable = element.closest('table[data-inside=field]') // Get parent table for data-show-field
        if(tablesWithInput.indexOf(inputTable) === -1) {
          tablesWithInput.push(inputTable)
        }
    })

    // Tables hide the entire column
    tablesWithInput.forEach(table => {
      showHideTableInputs(showWhenFields.length === countTrueConditions, table, fieldName, showWhenSubmit)
    })

    return;
  }

  let fieldContainer = inputElement.closest('.moonshine-field')
  if (fieldContainer === null) {
    fieldContainer = inputElement.closest('.form-group')
  }
  if (fieldContainer === null) {
    fieldContainer = inputElement
  }

  if (showWhenFields.length === countTrueConditions) {
    fieldContainer.style.removeProperty('display')

    const nameAttr = inputElement.getAttribute('data-show-when-column')
    if(nameAttr) {
      inputElement.setAttribute('name', nameAttr)
    }
  } else {
    fieldContainer.style.display = 'none'

    if(! showWhenSubmit) {
      const nameAttr = inputElement.getAttribute('name');
      if(nameAttr) {
        inputElement.setAttribute('data-show-when-column', nameAttr);
        inputElement.removeAttribute('name')
      }
    }
  }
}

function showHideTableInputs(isShow, table, fieldName, showWhenSubmit) {

  let cellIndexTd = null;

  table.querySelectorAll('[data-show-when-field="' + fieldName + '"]').forEach(element => {
    if(isShow) {
      element.closest('td').style.removeProperty('display')

      const nameAttr = element.getAttribute('data-show-when-column')
      if(nameAttr) {
        element.setAttribute('name', nameAttr)
      }
    } else {
      element.closest('td').style.display = 'none'

      if(! showWhenSubmit) {
        const nameAttr = element.getAttribute('name');
        if(nameAttr) {
          element.setAttribute('data-show-when-column', nameAttr);
          element.removeAttribute('name')
        }
      }
    }

    if(cellIndexTd === null) {
      cellIndexTd = element.closest('td').cellIndex
    }
  })

  if(cellIndexTd !== null) {
    table.querySelectorAll('th').forEach((element) => {
      if(element.cellIndex !== cellIndexTd) {
        return
      }
      element.style.display = isShow ? 'block' : 'none'
    })
  }
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

export function inputGetValue(element) {
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

function isShowField(fieldName, inputs, field) {
  let isShowField = false

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
      isShowField = valueInput == valueField
      break
    case '!=':
      isShowField = valueInput != valueField
      break
    case '>':
      isShowField = valueInput > valueField
      break
    case '<':
      isShowField = valueInput < valueField
      break
    case '>=':
      isShowField = valueInput >= valueField
      break
    case '<=':
      isShowField = valueInput <= valueField
      break
    case 'in':
      if (Array.isArray(valueInput) && Array.isArray(valueField)) {
        for (let i = 0; i < valueField.length; i++) {
          if (valueInput.includes(valueField[i])) {
            isShowField = true
            break
          }
        }
      } else {
        isShowField = valueField.includes(valueInput)
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
        isShowField = !includes
      } else {
        isShowField = !valueField.includes(valueInput)
      }
      break
  }

  return isShowField
}