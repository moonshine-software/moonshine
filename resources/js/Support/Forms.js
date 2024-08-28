import {inputFieldName, inputGetValue} from './ShowWhen.js'

export function filterAttributeStartsWith(data, startsWith) {
  const filtered = {}

  for (const key in data) {
    if (!key.startsWith(startsWith) && key !== 'column') {
      filtered[key] = data[key]
    }
  }

  return filtered
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

export function getAncestorsUntil(element, stopElement) {
  const ancestors = []
  let currentElement = element.parentNode

  while (currentElement && currentElement !== stopElement) {
    ancestors.push(currentElement)
    currentElement = currentElement.parentNode
  }

  return ancestors
}

export function containsAttribute(el, attr) {
  return el?.outerHTML?.includes(attr)
}

export function isTextInput(el) {
  let tagName = el?.tagName

  if (tagName === 'INPUT') {
    let validType = [
      'text',
      'password',
      'number',
      'email',
      'tel',
      'url',
      'search',
      'date',
      'datetime',
      'datetime-local',
      'time',
      'month',
      'week',
    ]

    return validType.includes(el.type)
  }

  return false
}

export function getQueryString(obj, encode = false) {
  function serialize(obj, prefix) {
    const queryStringParts = [];

    for (let key in obj) {
      if (obj.hasOwnProperty(key)) {
        const fullKey = prefix ? `${prefix}[${key}]` : key;
        const value = obj[key];

        if (typeof value === "object" && value !== null) {
          queryStringParts.push(serialize(value, fullKey));
        } else {
          queryStringParts.push(`${fullKey}=${value}`);
        }
      }
    }

    return queryStringParts.join("&");
  }

  const str = serialize(obj)
  return encode === true ? encodeURI(str) : str;
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
      values[inputFieldName(name)] = inputGetValue(element)
    }
  })

  return Object.entries(values)
    .map(x => `${encodeURIComponent(x[0])}=${encodeURIComponent(x[1])}`)
    .join('&')
}
