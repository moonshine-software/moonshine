import {inputFieldName, inputGetValue} from './Forms.js'

/**
 * We collect all the fields that participate in ShowWhen
 */
export function getInputs(formId) {
  const inputs = {}

  const form = document.getElementById(formId)

  form.querySelectorAll('[name]').forEach(element => {
    const name = element.getAttribute('name')
    const column = inputFieldName(name)
    const type = element.getAttribute('type')

    if (type === 'radio' && !element.checked) {
      return
    }

    inputs[column] = {
      value: inputGetValue(element),
      name: name,
      type: type,
    }
  })

  form.querySelectorAll('[data-show-when-field]').forEach(element => {
    const name = element.getAttribute('data-show-when-field')
    const column = inputFieldName(name)

    inputs[column] = {
      name,
      value: inputGetValue(element),
      type: element.getAttribute('type'),
    }
  })

  form.querySelectorAll('[data-show-when-column]').forEach(element => {
    const name = element.getAttribute('data-show-when-column')
    const column = inputFieldName(name)

    inputs[column] = {
      value: inputGetValue(element),
      name,
      type: element.getAttribute('type'),
    }
  })

  return inputs
}

/**
 * Triggered when a field changes on the onChangeField event
 *
 * Find related fields and trigger showWhenVisibilityChange for each
 */
export function showWhenChange(fieldName, formId) {
  let fieldColumn = inputFieldName(fieldName)

  const showWhenFields = []

  this.whenFields.forEach(field => {
    let inputElement = showWhenSelector(fieldName, formId)

    if (inputElement === null || inputElement === undefined) {
      return
    }

    /**
     * Paired fields (range, date range, etc.) use data-sync-with attribute
     * to reference their sibling field for synchronized show/hide behavior
     */
    let syncWith = inputElement.dataset.syncWith

    const isTargetField = fieldColumn === field.changeField || syncWith === field.changeField

    if (!field.is_row_mode && !isTargetField) {
      return
    }

    let showField = field.showField

    if (!showWhenFields[showField]) {
      showWhenFields[showField] = []
    }

    showWhenFields[showField].push(field)
  })

  for (let showField in showWhenFields) {
    this.showWhenVisibilityChange(
      showWhenFields[showField],
      showField,
      this.getInputs(formId),
      formId,
    )
  }
}

/**
 * Main function
 */
export function showWhenVisibilityChange(showWhenFields, fieldName, inputs, formId) {
  if (showWhenFields.length === 0) {
    return
  }

  let inputElement = showWhenSelector(fieldName, formId)

  if (inputElement === null || inputElement === undefined) {
    return
  }

  let visibleFieldsCount = 0

  showWhenFields.forEach(field => {
    if (isShowField(fieldName, inputs, field, formId)) {
      visibleFieldsCount++
    }
  })

  const showWhenSubmit = document.querySelector(`#${formId}`).getAttribute('data-submit-show-when')

  // If input is in a table, then find all tables with this input
  if (inputElement.closest('table[data-inside=field]')) {
    const tablesWithInput = []

    // Only data-show-when-field is used in tables, see in UI/Collections/Fields.php(prepareReindex)
    document
      .querySelectorAll('#' + formId + ' [data-show-when-field="' + fieldName + '"]')
      .forEach(function (element) {
        let inputTable = element.closest('table[data-inside=field]') // Get parent table for data-show-field
        if (tablesWithInput.indexOf(inputTable) === -1) {
          tablesWithInput.push(inputTable)
        }
      })

    // Tables hide the entire column
    tablesWithInput.forEach(table => {
      showHideTableInputs(
        showWhenFields.length === visibleFieldsCount,
        table,
        fieldName,
        showWhenSubmit,
      )
    })

    return
  }

  showHideField(showWhenFields.length === visibleFieldsCount, inputElement, showWhenSubmit)
}

function showWhenSelector(name, formId) {
  let inputElement = document.querySelector('#' + formId + ' [name="' + name + '"]')

  if (inputElement === null) {
    inputElement = document.querySelector(
      '#' + formId + ' [data-show-when-field="' + name + '"]',
    )
  }

  if (inputElement === null) {
    inputElement = document.querySelector(
      '#' + formId + ' [data-show-when-column="' + name + '"]',
    )
  }

  return inputElement
}

function showHideField(isShow, inputElementField, showWhenSubmit) {
  showHideInputElement(isShow, inputElementField, showWhenSubmit)

  // If inside the field there are entry fields with the name attribute
  let inputs = inputElementField.querySelectorAll('[name]')
  if (inputs.length === 0) {
    // If the fields were hidden, then their attribute name is data-show-when-column
    inputs = inputElementField.querySelectorAll('[data-show-when-column]')
  }
  for (let i = 0; i < inputs.length; i++) {
    showHideInputElement(isShow, inputs[i], showWhenSubmit)
  }
}

function showHideInputElement(isShow, inputElement, showWhenSubmit) {
  let fieldContainer = inputElement.closest('.moonshine-field')

  if (fieldContainer === null) {
    fieldContainer = inputElement.closest('.form-group')
  }

  if (fieldContainer === null) {
    fieldContainer = inputElement.closest('td')
  }

  if (isShow) {
    fieldContainer.classList.remove('hidden')

    const nameAttr = inputElement.getAttribute('data-show-when-column')

    if (nameAttr) {
      inputElement.setAttribute('name', nameAttr)
    }

    const requiredAttr = inputElement.getAttribute('data-required-when-column')

    if (nameAttr) {
      inputElement.setAttribute('required', requiredAttr)
    }
  } else {
    fieldContainer.classList.add('hidden')

    if (!showWhenSubmit) {
      const nameAttr = inputElement.getAttribute('name')

      if (nameAttr) {
        inputElement.setAttribute('data-show-when-column', nameAttr)
        inputElement.removeAttribute('name')
      }

      const requiredAttr = inputElement.getAttribute('required')

      if (requiredAttr) {
        inputElement.setAttribute('data-required-when-column', requiredAttr)
        inputElement.removeAttribute('required')
      }
    }
  }
}

function showHideTableInputs(isShow, table, fieldName, showWhenSubmit) {
  let cellIndexTd = null

  table.querySelectorAll('[data-show-when-field="' + fieldName + '"]').forEach(element => {
    if (element.dataset.objectMode) {
      showHideField(isShow, element)

      return
    }

    const td = element.closest('td')

    if (td.dataset.objectMode) {
      showHideField(isShow, element)

      return
    }

    if (isShow) {
      td.classList.remove('hidden')

      const nameAttr = element.getAttribute('data-show-when-column')
      if (nameAttr) {
        element.setAttribute('name', nameAttr)
      }
      const requiredAttr = element.getAttribute('data-required-when-column')
      if (requiredAttr) {
        element.setAttribute('required', requiredAttr)
      }
    } else {
      td.classList.add('hidden')

      if (!showWhenSubmit) {
        const nameAttr = element.getAttribute('name')
        if (nameAttr) {
          element.setAttribute('data-show-when-column', nameAttr)
          element.removeAttribute('name')
        }
        const requiredAttr = element.getAttribute('required')
        if (requiredAttr) {
          element.setAttribute('data-required-when-column', requiredAttr)
          element.removeAttribute('required')
        }
      }
    }

    if (cellIndexTd === null) {
      cellIndexTd = td.cellIndex
    }
  })

  if (cellIndexTd !== null) {
    table.querySelectorAll('th').forEach(element => {
      if (element.cellIndex !== cellIndexTd) {
        return
      }
      element.classList.toggle('hidden', !isShow)
    })
  }
}

function isShowField(fieldName, inputs, field, formId) {
  if (field.is_row_mode) {
    const showWhenSubmit = document.querySelector(`#${formId}`).getAttribute('data-submit-show-when')

    document
      .querySelectorAll('#' + formId + ' [data-show-when-field="' + fieldName + '"]')
      .forEach(function (element) {
        let row = element.closest('tr')
        let target = row.querySelector('[data-column="' + field.changeField + '"]')

        let isShow = isShowFieldCondition(
          target.type,
          field.operator,
          field.value,
          inputGetValue(target)
        )


        showHideField(isShow, element, showWhenSubmit)
      })

    return true
  }

  return isShowFieldCondition(
    inputs[field.changeField].type,
    field.operator,
    inputs[field.changeField].value,
    field.value,
  )
}

function isShowFieldCondition(inputType, operator, valueInput, valueField) {
  let isShowField = false

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

  switch (operator) {
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
          if (valueInput.some(v => v == valueField[i])) {
            isShowField = true
            break
          }
        }
      } else {
        isShowField =  Array.isArray(valueField)
          ? valueField.some(v => v == valueInput)
          : valueField.includes(valueInput);
      }
      break
    case 'not in':
      if (Array.isArray(valueInput) && Array.isArray(valueField)) {
        let includes = false
        for (let i = 0; i < valueField.length; i++) {
          if (valueInput.some(v => v == valueField[i])) {
            includes = true
            break
          }
        }
        isShowField = !includes
      } else {
        isShowField = Array.isArray(valueField)
          ? !valueField.some(v => v == valueInput)
          : !valueField.includes(valueInput);
      }
      break
  }

  return isShowField
}
