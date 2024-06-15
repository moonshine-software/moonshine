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

export function getAncestorsUntil(element, stopElement) {
  const ancestors = []
  let currentElement = element.parentNode

  while (currentElement && currentElement !== stopElement) {
    ancestors.push(currentElement)
    currentElement = currentElement.parentNode
  }

  return ancestors
}
