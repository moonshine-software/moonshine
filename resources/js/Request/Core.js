import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import {dispatchEvents} from '../Support/DispatchEvents.js'

export default function request(
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

        downloadFile(fileName, data)
      }

      const type = data.messageType ? data.messageType : 'success'

      if (data.message) {
        MoonShine.ui.toast(data.message, type)
      }

      if (componentRequestData.hasAfterCallback()) {
        componentRequestData.afterCallback(data, type, t)
      }

      const events = data.events ?? componentRequestData.events

      if (events) {
        dispatchEvents(events, type, t, componentRequestData.extraAttributes)
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

export function urlWithQuery(url, append, callback = null) {
  let urlObject = url.startsWith('/')
    ? new URL(url, window.location.origin)
    : new URL(url)

  if(callback !== null) {
    callback(urlObject)
  }

  let separator = urlObject.searchParams.size ? '&' : '?'

  return urlObject.toString() + separator + append
}

function responseCallback(callback, response, element, events, component) {
  const fn = MoonShine.callbacks[callback]

  if (typeof fn !== 'function') {
    MoonShine.ui.toast('Error', 'error')

    throw new Error(callback + ' is not a function!')
  }

  fn(response, element, events, component)
}

function beforeCallback(callback, element, component) {
  const fn = MoonShine.callbacks[callback]

  if (typeof fn !== 'function') {
    throw new Error(callback + ' is not a function!')
  }

  fn(element, component)
}

function downloadFile(fileName, data) {
  const url = window.URL.createObjectURL(new Blob([data]))
  const a = document.createElement('a')
  a.style.display = 'none'
  a.href = url
  a.download = fileName
  document.body.appendChild(a)
  a.click()
  window.URL.revokeObjectURL(url)
}
