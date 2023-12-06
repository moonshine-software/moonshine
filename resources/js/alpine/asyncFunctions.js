export function responseCallback(callback, response, element, events, component) {
  const fn = window[callback]

  if (typeof fn !== 'function') {
    component.$dispatch('toast', {type: 'error', text: 'Error'})

    throw new Error(callback + ' is not a function!')
  }

  fn(response, element, events, component)
}

export function dispatchEvents(events, type, component) {
  if (events !== '' && type !== 'error') {
    const allEvents = events.split(',')

    allEvents.forEach(event => component.$dispatch(event.replaceAll(/\s/g, '')))
  }
}
