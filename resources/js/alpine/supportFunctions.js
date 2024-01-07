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
