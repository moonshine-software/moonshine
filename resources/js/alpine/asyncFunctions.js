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

export function asyncCRUDRequest(component) {
  component.$event.preventDefault()

  let url = component.$el.href ? component.$el.href : component.asyncUrl

  component.loading = true

  if (component.$event.detail && component.$event.detail.filters) {
    url = prepareCRUDUrl(url)

    const urlWithFilters = new URL(url)

    let separator = urlWithFilters.searchParams.size ? '&' : '?'

    url = urlWithFilters.toString() + separator + component.$event.detail.filters
  }

  if (component.$event.detail && component.$event.detail.queryTag) {
    url = prepareCRUDUrl(url)

    if (component.$event.detail.queryTag !== 'query-tag=null') {
      const urlWithQueryTags = new URL(url)

      let separator = urlWithQueryTags.searchParams.size ? '&' : '?'

      url = urlWithQueryTags.toString() + separator + component.$event.detail.queryTag
    }
  }

  axios
    .get(url)
    .then(response => {
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

  function prepareCRUDUrl(url) {
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
