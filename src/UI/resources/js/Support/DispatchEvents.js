import DOMUpdater, {extractFromEventDetails} from './DOMUpdater.js'

export const fragmentUpdateWaitUntil = Symbol('fragmentUpdateWaitUntil')

export function dispatchEvents(
  events,
  type,
  component,
  extraProperties = {},
  waitForFragments = false,
) {
  if (!events) {
    return
  }

  if (typeof events !== 'string') {
    return
  }

  const fragmentUpdates = []

  if (events !== '') {
    const allEvents = events.split(',')

    allEvents.forEach(function (event) {
      let parts = event.split('|')

      const eventName = parts[0].replaceAll(/\s/g, '').toLowerCase()

      const attributes = {}
      Object.assign(attributes, extraProperties)

      if (Array.isArray(parts) && parts.length > 1) {
        let params = parts[1].split(';')

        for (let param of params) {
          let pair = param.split('~')
          attributes[pair[0]] = pair[1].replace(/`/g, '').trim()
        }
      }

      let finishDispatch
      const updates = []

      if (waitForFragments && eventName.startsWith('fragment_updated:')) {
        fragmentUpdates.push(new Promise(resolve => (finishDispatch = resolve)))
        attributes[fragmentUpdateWaitUntil] = update => updates.push(update)
      }

      setTimeout(function () {
        dispatchEvent(
          new CustomEvent(eventName, {
            detail: attributes,
            bubbles: true,
            composed: true,
            cancelable: true,
          }),
        )

        finishDispatch?.(Promise.allSettled(updates))

        DOMUpdater({
          htmlData: extractFromEventDetails(attributes.selectors, ['selector', 'html']),
          fields_values: extractFromEventDetails(attributes.fields_values),
        })
      }, attributes['_delay'] ?? 0)
    })
  }

  if (fragmentUpdates.length) {
    return Promise.all(fragmentUpdates)
  }
}
