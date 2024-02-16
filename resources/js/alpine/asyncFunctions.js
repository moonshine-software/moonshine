export function responseCallback(callback, response, element, events, component) {
  const fn = MoonShine.callbacks[callback]

  if (typeof fn !== 'function') {
    component.$dispatch('toast', {type: 'error', text: 'Error'})

    throw new Error(callback + ' is not a function!')
  }

  fn(response, element, events, component)
}

export function dispatchEvents(events, type, component) {
  if (events !== '' && type !== 'error') {
    const allEvents = events.split(',')

    allEvents.forEach(function (event) {
      let parts = event.split(':')

      let eventName = parts[0]

      let attributes = {}

      if (Array.isArray(parts) && parts.length > 1) {
        let params = parts[1].split(';')

        for (let param of params) {
          let pair = param.split('=')
          attributes[pair[0]] = pair[1].replace(/`/g, '').trim()
        }
      }

      component.$dispatch(eventName.replaceAll(/\s/g, ''), attributes)
    })
  }
}

export function moonShineRequest(t, url, method = 'get', body = {}, headers = {}) {
  if (!url) {
    return
  }

  axios({
    url: url,
    method: method,
    data: body,
    headers: headers,
  })
    .then(function (response) {
      const data = response.data

      if (t.beforeCallback !== undefined && typeof t.beforeCallback === 'function') {
        t.beforeCallback(data)
      }

      if (t.callback !== undefined && t.callback) {
        responseCallback(t.callback, response, t.$el, t.events, t)

        return
      }

      if (t.selector !== undefined && t.selector) {
        const element = document.querySelector(t.selector)
        element.innerHTML = data.html ? data.html : data
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

      const type = data.messageType ? data.messageType : 'success'

      if (data.message) {
        t.$dispatch('toast', {
          type: type,
          text: data.message,
        })
      }

      if (t.afterCallback !== undefined && typeof t.afterCallback === 'function') {
        t.afterCallback(data, type)
      }

      if (t.events !== undefined) {
        dispatchEvents(t.events, type, t)
      }
    })
    .catch(errorResponse => {
      if (!errorResponse?.response?.data) {
        console.error(errorResponse)

        return
      }

      const data = errorResponse.response.data

      if (t.errorCallback !== undefined && typeof t.errorCallback === 'function') {
        t.errorCallback(data)
      }

      if (t.callback !== undefined && t.callback) {
        responseCallback(t.callback, errorResponse.response, t.$el, t.events, t)

        return
      }

      if (t.afterErrorCallback !== undefined && typeof t.afterErrorCallback === 'function') {
        t.afterErrorCallback(data)
      }

      t.$dispatch('toast', {type: 'error', text: data.message ?? data})
    })
}

export function listComponentRequest(component) {
  component.$event.preventDefault()

  let url = component.$el.href ? component.$el.href : component.asyncUrl

  component.loading = true

  if (component.$event.detail && component.$event.detail.filters) {
    url = prepareListComponentRequestUrl(url)

    const urlWithFilters = new URL(url)

    let separator = urlWithFilters.searchParams.size ? '&' : '?'

    url = urlWithFilters.toString() + separator + component.$event.detail.filters
  }

  if (component.$event.detail && component.$event.detail.queryTag) {
    url = prepareListComponentRequestUrl(url)

    if (component.$event.detail.queryTag !== 'query-tag=null') {
      const urlWithQueryTags = new URL(url)

      let separator = urlWithQueryTags.searchParams.size ? '&' : '?'

      url = urlWithQueryTags.toString() + separator + component.$event.detail.queryTag
    }
  }

  axios
    .get(url)
    .then(response => {
      if (component.$root.dataset.events) {
        dispatchEvents(component.$root.dataset.events, 'success', component)
      }

      if (
        component.$root.getAttribute('data-pushstate') !== null &&
        component.$root.getAttribute('data-pushstate')
      ) {
        const query = url.slice(url.indexOf('?') + 1)

        history.pushState({}, '', query ? '?' + query : location.pathname)
      }
      component.$root.outerHTML = response.data
      component.loading = false
    })
    .catch(error => {
      component.loading = false
    })

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
