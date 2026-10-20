import selectorsParams from '../Support/SelectorsParams.js'
import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import request, {initCallback} from '../Request/Core.js'
import {getQueryString} from '../Support/Forms.js'
import {mergeURLString} from '../Support/URLs.js'

export default (asyncUpdateRoute = '') => ({
  asyncUpdateRoute: asyncUpdateRoute,
  withParams: '',
  withQueryParams: false,
  loading: false,

  init() {
    this.loading = false
    this.withParams = this.$el?.dataset?.asyncWithParams
    this.withQueryParams = this.$el?.dataset?.asyncWithQueryParams ?? false
  },
  async fragmentUpdate(events = '', callback = {}) {
    if (typeof events !== 'string') {
      events = ''
    }

    if (this.asyncUpdateRoute === '') {
      return
    }

    if (this.loading) {
      return
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

    const eventDetailQuery = this.$event ? getQueryString(this.$event.detail) : null;

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

    request(t, t.asyncUpdateRoute, 'get', body, {}, componentRequestData)
  },
})
