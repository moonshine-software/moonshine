export function dispatchEvents(events, type, component, extraAttributes = {}) {
  if (events.includes('{row-id}') && component.$el !== undefined) {
    const tr = component.$el.closest('tr')
    events = events.replace(/{row-id}/g, tr?.dataset?.rowKey ?? 0)
  }

  if (events !== '' && type !== 'error') {
    const allEvents = events.split(',')

    allEvents.forEach(function (event) {
      let parts = event.split('|')

      let eventName = parts[0]

      let attributes = extraAttributes

      if (Array.isArray(parts) && parts.length > 1) {
        let params = parts[1].split(';')

        for (let param of params) {
          let pair = param.split('=')
          attributes[pair[0]] = pair[1].replace(/`/g, '').trim()
        }
      }

      component.$dispatch(eventName.replaceAll(/\s/g, '').toLowerCase(), attributes)
    })
  }
}
