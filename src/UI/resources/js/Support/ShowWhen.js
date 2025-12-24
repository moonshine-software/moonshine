import {inputFieldName, inputGetValue} from './Forms.js'

/**
 * We collect all the fields that participate in ShowWhen
 */
export function getShowWhenInputs(formId) {
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
      type: type,
    }
  })

  form.querySelectorAll('[data-show-when-field]').forEach(element => {
    const name = element.getAttribute('data-show-when-field')
    const column = inputFieldName(name)

    inputs[column] = {
      value: name,
      type: element.getAttribute('type'),
    }
  })

  form.querySelectorAll('[data-show-when-column]').forEach(element => {
    const name = element.getAttribute('data-show-when-column')
    const column = inputFieldName(name)

    inputs[column] = {
      value: inputGetValue(element),
      type: element.getAttribute('type'),
    }
  })

  return inputs
}

/**
 * Triggered when a field changes on the onChangeField event
 *
 * Find related fields and trigger showWhenUpdateVisibility for each
 */
export function showWhenChange(fieldName, formId) {
  let fieldColumn = inputFieldName(fieldName)

  const relatedFields = []

  this.whenFields.forEach(field => {
    let fieldElement = findFieldElement(fieldName, formId)

    if (fieldElement === null || fieldElement === undefined) {
      return
    }

    /**
     * Paired fields (range, date range, etc.) use data-sync-with attribute
     * to reference their sibling field for synchronized show/hide behavior
     */
    let syncWith = fieldElement.dataset.syncWith

    const isTargetField = fieldColumn === field.changeField || syncWith === field.changeField

    if (!field.is_row_mode && !isTargetField) {
      return
    }

    let showField = field.showField

    if (!relatedFields[showField]) {
      relatedFields[showField] = []
    }

    relatedFields[showField].push(field)
  })

  for (let showField in relatedFields) {
    this.showWhenUpdateVisibility(
      relatedFields[showField],
      showField,
      this.getShowWhenInputs(formId),
      formId,
    )
  }
}

/**
 * Main function
 */
export function showWhenUpdateVisibility(relatedFields, fieldName, inputs, formId) {
  if (relatedFields.length === 0) {
    return
  }

  let fieldElement = findFieldElement(fieldName, formId)

  if (fieldElement === null || fieldElement === undefined) {
    return
  }

  let matchedConditions = 0

  const keepName = document.querySelector(`#${formId}`).getAttribute('data-submit-show-when')

  relatedFields.forEach(field => {
    if (shouldShowField(fieldName, inputs, field, formId, keepName)) {
      matchedConditions++
    }
  })

  /**
   * In row mode, visibility is handled per-row in shouldShowField,
   * including th hiding when all cells are hidden
   */
  if (fieldElement.dataset.isRowMode) {
    return
  }

  // If input is in a table, then find all tables with this input
  if (fieldElement.closest('table[data-inside=field]')) {
    const relatedTables = []

    // Only data-show-when-field is used in tables, see in UI/Collections/Fields.php(prepareReindex)
    document
      .querySelectorAll('#' + formId + ' [data-show-when-field="' + fieldName + '"]')
      .forEach(function (element) {
        let parentTable = element.closest('table[data-inside=field]') // Get parent table for data-show-field
        if (relatedTables.indexOf(parentTable) === -1) {
          relatedTables.push(parentTable)
        }
      })

    // Tables hide the entire column
    relatedTables.forEach(table => {
      toggleTableColumn(relatedFields.length === matchedConditions, table, fieldName, keepName)
    })

    return
  }

  toggleField(relatedFields.length === matchedConditions, fieldElement, keepName)
}

function findFieldElement(name, formId) {
  let element = document.querySelector('#' + formId + ' [name="' + name + '"]')

  if (element === null) {
    element = document.querySelector('#' + formId + ' [data-show-when-field="' + name + '"]')
  }

  if (element === null) {
    element = document.querySelector('#' + formId + ' [data-show-when-column="' + name + '"]')
  }

  return element
}

function toggleField(isShow, fieldElement, keepName) {
  toggleInputElement(isShow, fieldElement, keepName)

  // If inside the field there are entry fields with the name attribute
  let inputs = fieldElement.querySelectorAll('[name]')
  if (inputs.length === 0) {
    // If the fields were hidden, then their attribute name is data-show-when-column
    inputs = fieldElement.querySelectorAll('[data-show-when-column]')
  }
  for (let i = 0; i < inputs.length; i++) {
    toggleInputElement(isShow, inputs[i], keepName)
  }
}

function toggleInputElement(isShow, element, keepName) {
  let container = element.closest('.moonshine-field')
  let siblingElements = []

  if (container === null) {
    container = element.closest('.form-group')
  }

  if (container === null) {
    container = element
    /** tom-select without container problem */
    const nextElement = container.nextElementSibling
    if (nextElement?.classList.contains('ts-wrapper')) {
      siblingElements.push(nextElement)
    }
  }

  applyVisibility(isShow, container, element, keepName, siblingElements)
}

function applyVisibility(isShow, container, element, keepName, siblingElements = []) {
  if (isShow) {
    container.classList.remove('hidden')
    siblingElements.forEach(el => el.classList.remove('hidden'))

    const nameAttr = element.getAttribute('data-show-when-column')

    if (nameAttr) {
      element.setAttribute('name', nameAttr)
    }

    const requiredAttr = element.getAttribute('data-required-when-column')

    if (requiredAttr) {
      element.setAttribute('required', requiredAttr)
    }
  } else {
    container.classList.add('hidden')
    siblingElements.forEach(el => el.classList.add('hidden'))

    if (!keepName) {
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
}

function toggleTableColumn(isShow, table, fieldName, keepName) {
  let columnIndex = null

  table.querySelectorAll('[data-show-when-field="' + fieldName + '"]').forEach(element => {
    const cell = toggleTableCell(isShow, element, keepName)

    if (cell === null) {
      return
    }

    if (columnIndex === null) {
      columnIndex = cell.cellIndex
    }
  })

  if (columnIndex !== null) {
    table.querySelectorAll('th').forEach(header => {
      if (header.cellIndex !== columnIndex) {
        return
      }
      header.classList.toggle('hidden', !isShow)
    })
  }
}

function toggleTableCell(isShow, element, keepName) {
  if (element.dataset.objectMode) {
    toggleField(isShow, element)

    return null
  }

  const cell = element.closest('td')

  if (cell.dataset.objectMode) {
    toggleField(isShow, element)

    return null
  }

  let siblingElements = []

  /** tom-select without container problem */
  const nextElement = element.nextElementSibling
  if (nextElement?.classList.contains('ts-wrapper')) {
    siblingElements.push(nextElement)
  }

  applyVisibility(isShow, cell, element, keepName, siblingElements)

  return cell
}

/**
 * Check if field should be visible based on conditions
 */
function shouldShowField(fieldName, inputs, field, formId, keepName) {
  if (field.is_row_mode) {
    let visibleCellsCount = 0
    let columnIndex = null
    let table = null

    document
      .querySelectorAll('#' + formId + ' [data-show-when-field="' + fieldName + '"]')
      .forEach(function (element) {
        let row = element.closest('tr')
        let target = row.querySelector('[data-column="' + field.changeField + '"]')

        let isVisible = evaluateCondition(
          target.type,
          field.operator,
          field.value,
          inputGetValue(target),
        )

        element.setAttribute('data-is-row-mode', 'true')

        toggleTableCell(isVisible, element, keepName)

        if (isVisible) {
          visibleCellsCount++
        }

        // Remember columnIndex and table for hiding th
        if (columnIndex === null) {
          const cell = element.closest('td')
          if (cell) {
            columnIndex = cell.cellIndex
            table = element.closest('table[data-inside=field]')
          }
        }
      })

    // Hide th if all cells in the column are hidden
    if (table !== null && columnIndex !== null) {
      table.querySelectorAll('th').forEach(header => {
        if (header.cellIndex === columnIndex) {
          header.classList.toggle('hidden', visibleCellsCount === 0)
        }
      })
    }

    return true
  }

  return evaluateCondition(
    inputs[field.changeField].type,
    field.operator,
    inputs[field.changeField].value,
    field.value,
  )
}

function evaluateCondition(inputType, operator, inputValue, conditionValue) {
  let result = false

  if (inputType === 'number') {
    inputValue = parseFloat(inputValue)
    conditionValue = parseFloat(conditionValue)
  } else if (inputType === 'date' || inputType === 'datetime-local') {
    if (inputType === 'date') {
      inputValue = inputValue + ' 00:00:00'
    }
    inputValue = new Date(inputValue).getTime()

    if (!Array.isArray(conditionValue)) {
      conditionValue = new Date(conditionValue).getTime()
    }
  }

  /**
   * Using loose equality (==) intentionally to compare values of different types
   * e.g., string "1" should match number 1 from form inputs
   */
  switch (operator) {
    case '=':
      result = inputValue == conditionValue
      break
    case '!=':
      result = inputValue != conditionValue
      break
    case '>':
      result = inputValue > conditionValue
      break
    case '<':
      result = inputValue < conditionValue
      break
    case '>=':
      result = inputValue >= conditionValue
      break
    case '<=':
      result = inputValue <= conditionValue
      break
    case 'in':
      if (Array.isArray(inputValue) && Array.isArray(conditionValue)) {
        for (let i = 0; i < conditionValue.length; i++) {
          if (inputValue.some(v => v == conditionValue[i])) {
            result = true
            break
          }
        }
      } else {
        result = Array.isArray(conditionValue)
          ? conditionValue.some(v => v == inputValue)
          : conditionValue.includes(inputValue)
      }
      break
    case 'not in':
      if (Array.isArray(inputValue) && Array.isArray(conditionValue)) {
        let includes = false
        for (let i = 0; i < conditionValue.length; i++) {
          if (inputValue.some(v => v == conditionValue[i])) {
            includes = true
            break
          }
        }
        result = !includes
      } else {
        result = Array.isArray(conditionValue)
          ? !conditionValue.some(v => v == inputValue)
          : !conditionValue.includes(inputValue)
      }
      break
  }

  return result
}
