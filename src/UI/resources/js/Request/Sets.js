import {dispatchEvents} from '../Support/DispatchEvents.js'
import request, {prepareUrl, urlWithQuery} from './Core.js'
import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import {getQueryString, prepareFormQueryString} from '../Support/Forms.js'

export function listRowRequest(component, key = null, index = null, type = 'change') {
  const body = component.table.querySelector('tbody')
  const isChange = type === 'change'
  const isRemove = type === 'remove'

  let tr = body.querySelector('[data-row-key="' + key + '"]')

  if ((isChange || isRemove) && tr === null) {
    return
  }

  if (isRemove) {
    tr.remove()
    component.initColumnSelection()

    return
  }

  axios
    .get(prepareUrl(component.asyncUrl + `&_key=${key}&_index=${index}`))
    .then(response => {
      const html = response.data.trim()

      if (isChange) {
        tr.outerHTML = html
      } else {
        const template = document.createElement('template')
        template.innerHTML = html

        tr = template.content.firstElementChild
      }

      if (tr === null || tr.tagName !== 'TR') {
        return
      }

      if (type === 'prepend') {
        body.prepend(tr)
      } else if (type === 'append') {
        body.appendChild(tr)
      }

      component.initColumnSelection()
    })
    .catch(error => {})
}

export function listComponentRequest(component, pushState = false, after = null, prefix = null) {
  component.$event.preventDefault()

  const queryPrefix = prefix ?? ''

  let url = component.$el.href ? component.$el.href : component.asyncUrl

  component.loading = true

  let eventData = component.$event.detail

  if (component?.$root?.dataset?.filtersFormName) {
    const form = document.querySelector(
      `form[data-component="${component.$root.dataset.filtersFormName}"]`,
    )

    url = prepareListComponentRequestUrl(url, queryPrefix)
    url = urlWithQuery(url, prepareFormQueryString(new FormData(form), '_token,_component_name'))
  }

  if (eventData && eventData.filterQuery) {
    url = prepareListComponentRequestUrl(url, queryPrefix)
    url = urlWithQuery(url, eventData.filterQuery)
    delete eventData.filterQuery
  }

  if (eventData && eventData.queryTag) {
    url = prepareListComponentRequestUrl(url, queryPrefix)
    url = urlWithQuery(url, eventData.queryTag)
    delete eventData.queryTag
  }

  if (eventData && eventData.page) {
    url = prepareListComponentRequestUrl(url, queryPrefix)
    url = urlWithQuery(url, `${queryPrefix}page=${eventData.page}`)
    delete eventData.page
  }

  if (eventData && eventData.sort) {
    url = prepareListComponentRequestUrl(url, queryPrefix)
    url = urlWithQuery(url, `${queryPrefix}sort=${eventData.sort}`)
    delete eventData.sort
  }

  let events = ''

  if (eventData && eventData.events) {
    events = eventData.events
    delete eventData.events
  }

  const originalUrl = url

  url = urlWithQuery(url, getQueryString(eventData))

  let stopLoading = function (data, t) {
    t.loading = false
  }

  let componentRequestData = new ComponentRequestData()
  componentRequestData
    .withBeforeHandleResponse(function (data, t) {
      let query = originalUrl.slice(originalUrl.indexOf('?') + 1)
      const params = new URLSearchParams(query)
      params.delete('_component_name')

      query = params.toString()

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

      let tempElement = document.createElement('div')
      tempElement.innerHTML = data

      Alpine.morph(t.$root, tempElement.firstElementChild.innerHTML, {
        key(el) {
          return el.dataset.rowKey ?? el.rowIndex ?? el.id
        },
      })

      t.loading = false
    })
    .withEvents(events)
    .withErrorCallback(stopLoading)

  request(component, url, 'get', {}, {}, componentRequestData).then(() => {
    if (typeof after === 'function') {
      after()
    }
  })

  function prepareListComponentRequestUrl(url, prefix = null) {
    const queryPrefix = prefix ?? ''
    const resultUrl = url.startsWith('/') ? new URL(url, window.location.origin) : new URL(url)

    if (resultUrl.searchParams.get(`${queryPrefix}reset`)) {
      resultUrl.searchParams.delete(`${queryPrefix}reset`)
    }

    if (resultUrl.searchParams.get(`${queryPrefix}query-tag`)) {
      resultUrl.searchParams.delete(`${queryPrefix}query-tag`)
    }

    Array.from(resultUrl.searchParams).map(function (values) {
      let [index] = values

      if (index.indexOf(`${queryPrefix}filter[`) === 0) {
        resultUrl.searchParams.delete(index)
      }

      if (index.indexOf('_data[') === 0) {
        resultUrl.searchParams.delete(index)
      }
    })

    return resultUrl.toString()
  }
}
