import {inputFieldName, inputGeValue} from './showWhenFunctions'
import {getAncestorsUntil} from './supportFunctions.js'

export function filterAttributeStartsWith(data, startsWith) {
  const filtered = {}

  for (const key in data) {
    if (!key.startsWith(startsWith) && key !== 'column') {
      filtered[key] = data[key]
    }
  }

  return filtered
}

export function crudFormQuery(formElements = null) {
  if (formElements.length === 0) {
    return ''
  }

  const values = {}
  formElements.forEach(element => {
    const name = element.getAttribute('name')

    if (
      element.getAttribute('type') !== 'file' &&
      element.tagName.toLowerCase() !== 'textarea' &&
      !name.startsWith('_') &&
      !name.startsWith('hidden_')
    ) {
      values[inputFieldName(name)] = inputGeValue(element)
    }
  })

  return Object.entries(values)
    .map(x => `${encodeURIComponent(x[0])}=${encodeURIComponent(x[1])}`)
    .join('&')
}

export function validationInHiddenBlocks() {
  const fields = document.querySelectorAll('input, select, textarea')

  for (const field of fields) {
    addInvalidListener(field)
  }
}

export function addInvalidListener(field) {
  field.addEventListener('invalid', function (event) {
    const element = event.target
    const form = event.target.closest('form')

    for (const ancestor of getAncestorsUntil(element, form)) {
      if (ancestor instanceof Element) {
        switch (true) {
          case ancestor.classList.contains('tab-panel'):
            ancestor.dispatchEvent(new Event('set-active-tab'))
            break
          case ancestor.classList.contains('accordion'):
            ancestor.dispatchEvent(new Event('collapse-open'))
            break
        }
      }
    }
  })
}
