import {dispatchEvents} from '../Support/DispatchEvents.js'
import request, {urlWithQuery} from './Core.js'
import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'

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

  if (component.$event.detail && component.$event.detail.page) {
    url = prepareListComponentRequestUrl(url)
    url = urlWithQuery(url, `page=${component.$event.detail.page}`)
  }

  if (component.$event.detail && component.$event.detail.sort) {
    url = prepareListComponentRequestUrl(url)
    url = urlWithQuery(url, `sort=${component.$event.detail.sort}`)
  }

  let stopLoading = function (data, t) {
    t.loading = false
  }

  let componentRequestData = new ComponentRequestData()
  componentRequestData
    .withBeforeCallback(function (data, t) {
      const query = url.slice(url.indexOf('?') + 1)

      if (pushState) {
        history.pushState({}, '', query ? '?' + query : location.pathname)
      }

      document.querySelectorAll('.js-change-query').forEach(function (element) {
        let value = element.dataset.originalUrl + (query ? '?' + query : '')

        if (element.dataset.originalQuery) {
          value =
            value +
            (query ? '&' + element.dataset.originalQuery : '?' + element.dataset.originalQuery)
        }

        let attr = 'href'

        if (element.tagName.toLowerCase() === 'form') {
          attr = 'action'
        }

        if (element.tagName.toLowerCase() === 'input') {
          attr = 'value'
        }

        element.setAttribute(attr, value)
      })

      if (t.$root.dataset.events) {
        dispatchEvents(t.$root.dataset.events, 'success', t)
      }

      t.$root.outerHTML = data
      t.loading = false
    })
    .withErrorCallback(stopLoading)

  request(component, url, 'get', {}, {}, componentRequestData)

  function prepareListComponentRequestUrl(url) {
    const resultUrl = url.startsWith('/') ? new URL(url, window.location.origin) : new URL(url)

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
