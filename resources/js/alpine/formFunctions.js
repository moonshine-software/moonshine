import {inputFieldName, inputGeValue} from './showWhenFunctions'

export function crudFormQuery() {
  const formElements = document.querySelectorAll('#moonshine-form [name]')

  if (formElements.length === 0) {
    return ''
  }

  const values = {}
  formElements.forEach(element => {
    const name = element.getAttribute('name')

    if (
      element.getAttribute('type') !== 'file' &&
      element.tagName.toLowerCase() !== 'textarea' &&
      name !== '_token' &&
      name !== '_method' &&
      !name.startsWith('hidden_')
    ) {
      values[inputFieldName(name)] = inputGeValue(element)
    }
  })

  return Object.entries(values)
    .map(x => `${encodeURIComponent(x[0])}=${encodeURIComponent(x[1])}`)
    .join('&')
}
