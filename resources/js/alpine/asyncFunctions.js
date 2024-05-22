import {ComponentRequestData} from '../moonshine.js'

export function responseCallback(callback, response, element, events, component) {
  const fn = MoonShine.callbacks[callback]

  if (typeof fn !== 'function') {
    MoonShine.ui.toast('Error', 'error')

    throw new Error(callback + ' is not a function!')
  }

  fn(response, element, events, component)
}

export function beforeCallback(callback, element, component) {
  const fn = MoonShine.callbacks[callback]

  if (typeof fn !== 'function') {
    throw new Error(callback + ' is not a function!')
  }

  fn(element, component)
}

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

export function moonShineRequest(
  t,
  url,
  method = 'get',
  body = {},
  headers = {},
  componentRequestData = {},
) {
  if (!url) {
    return
  }

  if (!(componentRequestData instanceof ComponentRequestData)) {
    componentRequestData = new ComponentRequestData()
  }

  if (componentRequestData.hasBeforeFunction()) {
    beforeCallback(componentRequestData.beforeFunction, t.$el, t)
  }

  axios({
    url: url,
    method: method,
    data: body,
    headers: headers,
  })
    .then(function (response) {
      t.loading = false

      const data = response.data
      const contentDisposition = response.headers['content-disposition']

      if (componentRequestData.hasBeforeCallback()) {
        componentRequestData.beforeCallback(data, t)
      }

      if (componentRequestData.hasResponseFunction()) {
        responseCallback(
          componentRequestData.responseFunction,
          response,
          t.$el,
          componentRequestData.events,
          t,
        )

        return
      }

      if (componentRequestData.selector) {
        const elements = document.querySelectorAll(componentRequestData.selector)
        elements.forEach(element => {
          element.innerHTML = data.html ? data.html : data
        })
      }

      if (data.fields_values !== undefined) {
        for (let [selector, value] of Object.entries(data.fields_values)) {
          let el = document.querySelector(selector)
          if (el !== null) {
            el.value = value
            el.dispatchEvent(new Event('change'))
          }
        }
      }

      if (data.redirect) {
        window.location = data.redirect
      }

      if (contentDisposition?.startsWith('attachment')) {
        let fileName = contentDisposition.split('filename=')[1]

        const url = window.URL.createObjectURL(new Blob([data]))
        const a = document.createElement('a')
        a.style.display = 'none'
        a.href = url
        a.download = fileName
        document.body.appendChild(a)
        a.click()
        window.URL.revokeObjectURL(url)
      }

      const type = data.messageType ? data.messageType : 'success'

      if (data.message) {
        MoonShine.ui.toast(data.message, type)
      }

      if (componentRequestData.hasAfterCallback()) {
        componentRequestData.afterCallback(data, type, t)
      }

      if (componentRequestData.events) {
        dispatchEvents(componentRequestData.events, type, t, componentRequestData.extraAttributes)
      }
    })
    .catch(errorResponse => {
      t.loading = false

      if (!errorResponse?.response?.data) {
        console.error(errorResponse)

        return
      }

      const data = errorResponse.response.data

      if (componentRequestData.hasErrorCallback()) {
        componentRequestData.errorCallback(data, t)
      }

      if (componentRequestData.hasResponseFunction()) {
        responseCallback(
          componentRequestData.responseFunction,
          errorResponse.response,
          t.$el,
          componentRequestData.events,
          t,
        )

        return
      }

      if (componentRequestData.hasAfterErrorCallback()) {
        componentRequestData.afterErrorCallback(data, t)
      }

      MoonShine.ui.toast(data.message ?? data, 'error')
    })
}

export function listComponentRequest(component, pushState = false) {
  component.$event.preventDefault()

  let url = component.$el.href ? component.$el.href : component.asyncUrl

  component.loading = true

  if (component.$event.detail && component.$event.detail.filterQuery) {
    url = prepareListComponentRequestUrl(url)
    url = appendQueryToUrl(url, component.$event.detail.filterQuery)
  }

  if (component.$event.detail && component.$event.detail.queryTag) {
    url = prepareListComponentRequestUrl(url)

    if (component.$event.detail.queryTag !== 'query-tag=null') {
      url = appendQueryToUrl(url, component.$event.detail.queryTag)
    }
  }

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

  function appendQueryToUrl(url, append) {
    const urlObject = new URL(url)

    let separator = urlObject.searchParams.size ? '&' : '?'

    return urlObject.toString() + separator + append
  }

  function prepareListComponentRequestUrl(url) {
    const resultUrl = new URL(url)

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
