import {dispatchEvents} from '../Support/DispatchEvents.js'
import {urlWithQuery} from './Core.js'

export function listComponentRequest(component, pushState = false) {
  component.$event.preventDefault()

  let url = component.$el.href ? component.$el.href : component.asyncUrl

  component.loading = true

  if (component.$event.detail && component.$event.detail.filterQuery) {
    url = prepareListComponentRequestUrl(url)
    url = urlWithQuery(url, component.$event.detail.filterQuery)
  }

  if (component.$event.detail && component.$event.detail.queryTag) {
    url = prepareListComponentRequestUrl(url)
    url = urlWithQuery(url, component.$event.detail.queryTag)
  }

  // todo change to Request
  axios
    .get(url)
    .then(response => {
      const query = url.slice(url.indexOf('?') + 1)

      if (pushState) {
        history.pushState({}, '', query ? '?' + query : location.pathname)
      }

      document.querySelectorAll('._change-query').forEach(function (element) {
        element.setAttribute('href', element.dataset.originalUrl + (query ? '?' + query : ''))
      })

      if (component.$root.dataset.events) {
        dispatchEvents(component.$root.dataset.events, 'success', component)
      }

      component.$root.outerHTML = response.data
      component.loading = false
    })
    .catch(error => {
      component.loading = false
    })

  function prepareListComponentRequestUrl(url) {
    const resultUrl = url.startsWith('/')
      ? new URL(url, window.location.origin)
      : new URL(url)

    if (resultUrl.searchParams.get('query-tag')) {
      resultUrl.searchParams.delete('query-tag')
    }

    Array.from(resultUrl.searchParams).map(function (values) {
      let [index] = values
      if (index.indexOf('filters[') === 0) {
        resultUrl.searchParams.delete(index)
      }
    })

    return resultUrl.toString()
  }
}
