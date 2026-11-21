import selectorsParams from '../Support/SelectorsParams.js'
import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import request, {initCallback} from '../Request/Core.js'
import {getQueryString} from '../Support/Forms.js'
import {mergeURLString} from '../Support/URLs.js'
import {fragmentUpdateWaitUntil} from '../Support/DispatchEvents.js'

export default (asyncUpdateRoute = '') => ({
  asyncUpdateRoute: asyncUpdateRoute,
  withParams: '',
  withQueryParams: false,
  loading: false,
  fragmentRequest: null,
  pendingRequests: 0,

  get busy() {
    return this.fragmentRequest !== null || this.pendingRequests > 0
  },

  init() {
    this.loading = false
    this.withParams = this.$el?.dataset?.asyncWithParams
    this.withQueryParams = this.$el?.dataset?.asyncWithQueryParams ?? false
  },
  async fragmentLoading(until) {
    if (typeof until?.then !== 'function') {
      return
    }

    this.pendingRequests++

    try {
      await until
    } catch {
      // The request's error handler reports the failure.
    } finally {
      this.pendingRequests--
    }
  },
  fragmentUpdate(events = '', callback = {}) {
    if (this.asyncUpdateRoute === '') {
      return
    }

    const detail = this.$event?.detail

    if (!this.fragmentRequest) {
      this.fragmentRequest = Promise.resolve()
        .then(() => this.updateFragment(events, callback, detail))
        .finally(() => {
          this.fragmentRequest = null
        })
    }

    detail?.[fragmentUpdateWaitUntil]?.(this.fragmentRequest)

    return this.fragmentRequest
  },
  updateFragment(events, callback, detail) {
    if (typeof events !== 'string') {
      events = ''
    }

    callback = initCallback(callback)

    this.loading = true

    let body = selectorsParams(this.withParams)

    const t = this

    const bodyParams = new URLSearchParams(body)

    if (this.withQueryParams) {
      const queryParams = new URLSearchParams(window.location.search)
      for (const [key, value] of queryParams) {
        bodyParams.append(key, value)
      }
    }

    t.asyncUpdateRoute = mergeURLString(t.asyncUpdateRoute, bodyParams.toString())

    const eventDetailQuery = detail ? getQueryString(detail) : null

    if (eventDetailQuery) {
      t.asyncUpdateRoute = mergeURLString(t.asyncUpdateRoute, eventDetailQuery)
    }

    let stopLoading = function (data, t) {
      t.loading = false
    }

    let componentRequestData = new ComponentRequestData()
    componentRequestData
      .withEvents(events)
      .withBeforeRequest(callback.beforeRequest)
      .withBeforeHandleResponse(stopLoading)
      .withResponseHandler(callback.responseHandler)
      .withAfterResponse(function (data) {
        t.$root.outerHTML = data

        return callback.afterResponse
      })
      .withErrorCallback(stopLoading)

    return request(t, t.asyncUpdateRoute, 'get', body, {}, componentRequestData)
  },
})
