import DOMUpdater, {extractFromEventDetails} from './DOMUpdater.js'

export function dispatchEvents(events, type, component, extraProperties = {}) {
  if (!events) {
    return
  }

  if (typeof events !== 'string') {
    return
  }

  if (events !== '' && type !== 'error') {
    const allEvents = events.split(',')

    allEvents.forEach(function (event) {
      let parts = event.split('|')

      let eventName = parts[0]

      const attributes = {}
      Object.assign(attributes, extraProperties)

      if (Array.isArray(parts) && parts.length > 1) {
        let params = parts[1].split(';')

        for (let param of params) {
          let pair = param.split('~')
          attributes[pair[0]] = pair[1].replace(/`/g, '').trim()
        }
      }

      setTimeout(function () {
        dispatchEvent(
          new CustomEvent(eventName.replaceAll(/\s/g, '').toLowerCase(), {
            detail: attributes,
            bubbles: true,
            composed: true,
            cancelable: true,
          }),
        )

        DOMUpdater({
          htmlData: extractFromEventDetails(attributes.selectors, ['selector', 'html']),
          fields_values: extractFromEventDetails(attributes.fields_values),
        })
      }, attributes['_delay'] ?? 0)
    })
  }
}
