import {inputFieldName, inputGeValue} from './showWhenFunctions'

export function filterAttributeStartsWith(data, startsWith) {
  const filtered = {};

  for (const key in data) {
    if (!key.startsWith(startsWith)) {
      filtered[key] = data[key];
    }
  }

  return filtered;
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
